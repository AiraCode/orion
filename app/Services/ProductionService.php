<?php

namespace App\Services;

use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionService
{
    public function __construct(
        protected LedgerService $ledgerService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Start a Production Order for a Product
     */
    public function createProductionOrder(
        Company $company,
        ProductionLine $productionLine,
        Product $product,
        Warehouse $warehouse,
        int $quantity,
        string $startDate
    ): ProductionOrder {
        return DB::transaction(function () use ($company, $productionLine, $product, $warehouse, $quantity, $startDate) {
            // Check BOM materials availability
            $bomItems = $product->rawMaterials;
            if ($bomItems->isEmpty()) {
                throw new Exception("Product {$product->name} has no Bill of Materials defined!");
            }

            $totalMaterialCost = 0.00;

            foreach ($bomItems as $rawMat) {
                $requiredQty = $rawMat->pivot->quantity_required * $quantity;
                $invItem = InventoryItem::where('company_id', $company->id)
                    ->where('warehouse_id', $warehouse->id)
                    ->where('item_type', 'raw_material')
                    ->where('item_id', $rawMat->id)
                    ->first();

                if (!$invItem || $invItem->quantity < $requiredQty) {
                    $available = $invItem ? $invItem->quantity : 0;
                    throw new Exception("Insufficient raw material '{$rawMat->name}'. Required: {$requiredQty}, Available: {$available}");
                }

                $totalMaterialCost += $requiredQty * (float)$invItem->average_unit_cost;
            }

            // Deduct Raw Materials from inventory
            foreach ($bomItems as $rawMat) {
                $requiredQty = $rawMat->pivot->quantity_required * $quantity;
                $this->inventoryService->deductStock(
                    company: $company,
                    warehouse: $warehouse,
                    itemType: 'raw_material',
                    itemId: $rawMat->id,
                    quantity: $requiredQty,
                    transactionType: 'production_out',
                    transactionDate: $startDate
                );
            }

            $order = ProductionOrder::create([
                'company_id' => $company->id,
                'production_line_id' => $productionLine->id,
                'product_id' => $product->id,
                'quantity_ordered' => $quantity,
                'quantity_produced' => $quantity,
                'status' => 'completed',
                'start_date' => $startDate,
                'completion_date' => $startDate,
                'total_production_cost' => $totalMaterialCost,
            ]);

            $unitCost = $quantity > 0 ? $totalMaterialCost / $quantity : 0;

            // Add finished goods to inventory
            $this->inventoryService->addStock(
                company: $company,
                warehouse: $warehouse,
                itemType: 'finished_good',
                itemId: $product->id,
                quantity: $quantity,
                unitCost: $unitCost,
                transactionType: 'production_in',
                transactionDate: $startDate,
                refType: 'production_order',
                refId: $order->id
            );

            // Double Entry Ledger:
            // Debit: Finished Goods Inventory (1040)
            // Credit: Raw Materials Inventory (1030)
            $fgAcc = $this->ledgerService->getAccount($company, '1040');
            $rmAcc = $this->ledgerService->getAccount($company, '1030');

            if ($totalMaterialCost > 0) {
                $this->ledgerService->postTransaction(
                    company: $company,
                    date: $startDate,
                    description: "Production of {$quantity} units of {$product->name}",
                    lines: [
                        ['account_id' => $fgAcc->id, 'type' => 'debit', 'amount' => $totalMaterialCost, 'memo' => "Manufactured {$product->name}"],
                        ['account_id' => $rmAcc->id, 'type' => 'credit', 'amount' => $totalMaterialCost, 'memo' => "Raw materials consumed for #{$order->id}"],
                    ],
                    refType: 'production_order',
                    refId: $order->id
                );
            }

            // Update production line status
            $productionLine->status = 'running';
            $productionLine->current_utilization_pct = min(100, round(($quantity / max(1, $productionLine->daily_capacity_units * 30)) * 100, 2));
            $productionLine->save();

            return $order;
        });
    }
}
