<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\SalesChannel;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesService
{
    public function __construct(
        protected LedgerService $ledgerService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create and fulfill a sales order
     */
    public function processSalesOrder(
        Company $company,
        Customer $customer,
        SalesChannel $channel,
        Warehouse $warehouse,
        Product $product,
        int $quantity,
        float $unitPrice,
        string $orderDate
    ): SalesOrder {
        return DB::transaction(function () use ($company, $customer, $channel, $warehouse, $product, $quantity, $unitPrice, $orderDate) {
            // Check inventory
            $invItem = InventoryItem::where('company_id', $company->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('item_type', 'finished_good')
                ->where('item_id', $product->id)
                ->first();

            if (!$invItem || $invItem->quantity < $quantity) {
                $available = $invItem ? $invItem->quantity : 0;
                throw new Exception("Insufficient finished goods inventory for '{$product->name}'. Requested: {$quantity}, Available: {$available}");
            }

            $totalAmount = round($quantity * $unitPrice, 4);

            // Deduct finished goods stock & calculate COGS
            $totalCogs = $this->inventoryService->deductStock(
                company: $company,
                warehouse: $warehouse,
                itemType: 'finished_good',
                itemId: $product->id,
                quantity: $quantity,
                transactionType: 'sales_out',
                transactionDate: $orderDate
            );

            $unitCogs = $quantity > 0 ? round($totalCogs / $quantity, 4) : 0;

            $salesOrder = SalesOrder::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'sales_channel_id' => $channel->id,
                'order_date' => $orderDate,
                'status' => 'fulfilled',
                'total_amount' => $totalAmount,
                'total_cogs' => $totalCogs,
            ]);

            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cogs' => $unitCogs,
                'total_price' => $totalAmount,
            ]);

            // Double Entry Ledger:
            // 1. Revenue Entry:
            //    Debit: Cash & Cash Equivalents (1010) [totalAmount]
            //    Credit: Product Sales Revenue (4010) [totalAmount]
            $cashAcc = $this->ledgerService->getAccount($company, '1010');
            $revenueAcc = $this->ledgerService->getAccount($company, '4010');

            $this->ledgerService->postTransaction(
                company: $company,
                date: $orderDate,
                description: "Sales Order #{$salesOrder->id} - {$customer->name} ({$quantity}x {$product->name})",
                lines: [
                    ['account_id' => $cashAcc->id, 'type' => 'debit', 'amount' => $totalAmount, 'memo' => 'Cash received from customer'],
                    ['account_id' => $revenueAcc->id, 'type' => 'credit', 'amount' => $totalAmount, 'memo' => 'Revenue from sales order'],
                ],
                refType: 'sales_order',
                refId: $salesOrder->id
            );

            // 2. COGS Entry:
            //    Debit: Raw Materials Consumed / COGS (5010) [totalCogs]
            //    Credit: Finished Goods Inventory (1040) [totalCogs]
            if ($totalCogs > 0) {
                $cogsAcc = $this->ledgerService->getAccount($company, '5010');
                $fgInvAcc = $this->ledgerService->getAccount($company, '1040');

                $this->ledgerService->postTransaction(
                    company: $company,
                    date: $orderDate,
                    description: "COGS for Sales Order #{$salesOrder->id} ({$product->name})",
                    lines: [
                        ['account_id' => $cogsAcc->id, 'type' => 'debit', 'amount' => $totalCogs, 'memo' => 'Cost of Goods Sold'],
                        ['account_id' => $fgInvAcc->id, 'type' => 'credit', 'amount' => $totalCogs, 'memo' => 'Inventory reduced from sale'],
                    ],
                    refType: 'sales_order_cogs',
                    refId: $salesOrder->id
                );
            }

            return $salesOrder;
        });
    }
}
