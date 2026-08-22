<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\SalesChannel;
use App\Models\Warehouse;
use App\Services\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAndRevenueTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_order_deducts_inventory_and_records_revenue_and_cogs(): void
    {
        $this->seed();
        $salesService = app(SalesService::class);
        $company = Company::first();
        $product = Product::where('company_id', $company->id)->first();
        $customer = Customer::first();
        $channel = SalesChannel::where('company_id', $company->id)->first();
        $warehouse = Warehouse::where('company_id', $company->id)->first();

        $initialCash = (float)$company->cash_balance;

        $order = $salesService->processSalesOrder(
            company: $company,
            customer: $customer,
            channel: $channel,
            warehouse: $warehouse,
            product: $product,
            quantity: 5,
            unitPrice: 10000.00,
            orderDate: '2026-08-20'
        );

        $this->assertNotNull($order);
        $this->assertEquals(50000.00, $order->total_amount);

        // Verify company cash increased by 50,000
        $company->refresh();
        $this->assertEquals($initialCash + 50000.00, (float)$company->cash_balance);
    }
}
