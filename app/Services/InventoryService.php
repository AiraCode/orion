<?php

namespace App\Services;

use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    public function __construct(protected LedgerService $ledgerService)
    {
    }

    /**
     * Add inventory stock (raw material or finished good)
     */
    public function addStock(
        Company $company,
        Warehouse $warehouse,
        string $itemType, // raw_material, finished_good
        int $itemId,
        int $quantity,
        float $unitCost,
        string $transactionType, // purchase_in, production_in, adjustment
        string $transactionDate,
        ?string $refType = null,
        ?int $refId = null
    ): InventoryItem {
        return DB::transaction(function () use ($company, $warehouse, $itemType, $itemId, $quantity, $unitCost, $transactionType, $transactionDate, $refType, $refId) {
            $item = InventoryItem::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'warehouse_id' => $warehouse->id,
                    'item_type' => $itemType,
                    'item_id' => $itemId,
                ],
                [
                    'quantity' => 0,
                    'average_unit_cost' => 0.00,
                    'total_value' => 0.00,
                ]
            );

            // Weighted average cost calculation
            $oldValue = (float)$item->total_value;
            $oldQty = (int)$item->quantity;
            $newAddedValue = $quantity * $unitCost;
            $totalQty = $oldQty + $quantity;
            $newAvgCost = $totalQty > 0 ? ($oldValue + $newAddedValue) / $totalQty : $unitCost;

            $item->quantity = $totalQty;
            $item->average_unit_cost = round($newAvgCost, 4);
            $item->total_value = round($totalQty * $newAvgCost, 4);
            $item->save();

            // Update warehouse capacity
            $warehouse->current_capacity_units = InventoryItem::where('warehouse_id', $warehouse->id)->sum('quantity');
            $warehouse->save();

            // Log inventory transaction
            InventoryTransaction::create([
                'company_id' => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'transaction_type' => $transactionType,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $newAddedValue,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'transaction_date' => $transactionDate,
            ]);

            return $item;
        });
    }

    /**
     * Deduct inventory stock (consumption for production or sales shipment)
     */
    public function deductStock(
        Company $company,
        Warehouse $warehouse,
        string $itemType,
        int $itemId,
        int $quantity,
        string $transactionType, // production_out, sales_out, scrap
        string $transactionDate,
        ?string $refType = null,
        ?int $refId = null
    ): float {
        return DB::transaction(function () use ($company, $warehouse, $itemType, $itemId, $quantity, $transactionType, $transactionDate, $refType, $refId) {
            $item = InventoryItem::where('company_id', $company->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if (!$item || $item->quantity < $quantity) {
                $available = $item ? $item->quantity : 0;
                throw new Exception("Insufficient inventory for {$itemType} ID {$itemId}! Requested: {$quantity}, Available: {$available}");
            }

            $unitCost = (float)$item->average_unit_cost;
            $totalCost = round($quantity * $unitCost, 4);

            $item->quantity -= $quantity;
            $item->total_value = round($item->quantity * $unitCost, 4);
            $item->save();

            // Update warehouse capacity
            $warehouse->current_capacity_units = InventoryItem::where('warehouse_id', $warehouse->id)->sum('quantity');
            $warehouse->save();

            // Log inventory transaction
            InventoryTransaction::create([
                'company_id' => $company->id,
                'warehouse_id' => $warehouse->id,
                'item_type' => $itemType,
                'item_id' => $itemId,
                'transaction_type' => $transactionType,
                'quantity' => -$quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'transaction_date' => $transactionDate,
            ]);

            return $totalCost;
        });
    }

    /**
     * Get inventory valuation for a company
     */
    public function getInventoryValuation(Company $company): array
    {
        $rawMaterialsValue = InventoryItem::where('company_id', $company->id)
            ->where('item_type', 'raw_material')
            ->sum('total_value');

        $finishedGoodsValue = InventoryItem::where('company_id', $company->id)
            ->where('item_type', 'finished_good')
            ->sum('total_value');

        return [
            'raw_materials_value' => (float)$rawMaterialsValue,
            'finished_goods_value' => (float)$finishedGoodsValue,
            'total_inventory_value' => (float)($rawMaterialsValue + $finishedGoodsValue),
        ];
    }
}
