<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class ProcurementService
{
    public function __construct(
        protected LedgerService $ledgerService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create a Purchase Order for Raw Materials
     */
    public function createPurchaseOrder(
        Company $company,
        Supplier $supplier,
        Warehouse $warehouse,
        string $orderDate,
        array $items // [['raw_material_id' => 1, 'quantity' => 500, 'unit_price' => 12.50]]
    ): PurchaseOrder {
        return DB::transaction(function () use ($company, $supplier, $warehouse, $orderDate, $items) {
            $totalAmount = 0.00;
            foreach ($items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Check cash availability
            if ($company->cash_balance < $totalAmount) {
                throw new Exception("Insufficient cash for Purchase Order! Required: \${$totalAmount}, Available: \${$company->cash_balance}");
            }

            $orderDateCarbon = Carbon::parse($orderDate);
            $expectedDelivery = $orderDateCarbon->copy()->addDays($supplier->lead_time_days);

            $po = PurchaseOrder::create([
                'company_id' => $company->id,
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'order_date' => $orderDate,
                'expected_delivery_date' => $expectedDelivery->toDateString(),
                'status' => 'ordered',
                'total_amount' => $totalAmount,
            ]);

            foreach ($items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Post transaction in double-entry ledger:
            // Debit: Raw Materials Inventory (1030) or Goods in Transit
            // Credit: Cash (1010)
            $inventoryAcc = $this->ledgerService->getAccount($company, '1030');
            $cashAcc = $this->ledgerService->getAccount($company, '1010');

            $this->ledgerService->postTransaction(
                company: $company,
                date: $orderDate,
                description: "Purchase Order #{$po->id} - {$supplier->name}",
                lines: [
                    ['account_id' => $inventoryAcc->id, 'type' => 'debit', 'amount' => $totalAmount, 'memo' => "PO #{$po->id} Raw Materials"],
                    ['account_id' => $cashAcc->id, 'type' => 'credit', 'amount' => $totalAmount, 'memo' => "Payment for PO #{$po->id}"],
                ],
                refType: 'purchase_order',
                refId: $po->id
            );

            return $po;
        });
    }

    /**
     * Receive and fulfill a Purchase Order upon delivery date
     */
    public function fulfillPurchaseOrder(PurchaseOrder $po, string $deliveryDate): void
    {
        DB::transaction(function () use ($po, $deliveryDate) {
            if ($po->status === 'delivered') {
                return;
            }

            $warehouse = $po->warehouse ?? $po->company->warehouses()->first();

            foreach ($po->items as $item) {
                $this->inventoryService->addStock(
                    company: $po->company,
                    warehouse: $warehouse,
                    itemType: 'raw_material',
                    itemId: $item->raw_material_id,
                    quantity: $item->quantity,
                    unitCost: (float)$item->unit_price,
                    transactionType: 'purchase_in',
                    transactionDate: $deliveryDate,
                    refType: 'purchase_order',
                    refId: $po->id
                );
            }

            $po->status = 'delivered';
            $po->actual_delivery_date = $deliveryDate;
            $po->save();
        });
    }
}
