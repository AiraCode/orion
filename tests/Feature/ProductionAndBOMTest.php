<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductionLine;
use App\Models\Warehouse;
use App\Services\ProductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionAndBOMTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_order_consumes_bom_and_creates_finished_goods(): void
    {
        $this->seed();
        $productionService = app(ProductionService::class);
        $company = Company::first();
        $product = Product::where('company_id', $company->id)->first();
        $line = ProductionLine::where('product_id', $product->id)->first() ?? ProductionLine::first();
        $warehouse = Warehouse::where('company_id', $company->id)->first();

        $fgInvBefore = InventoryItem::where('warehouse_id', $warehouse->id)
            ->where('item_type', 'finished_good')
            ->where('item_id', $product->id)
            ->value('quantity') ?? 0;

        $order = $productionService->createProductionOrder(
            company: $company,
            productionLine: $line,
            product: $product,
            warehouse: $warehouse,
            quantity: 10,
            startDate: '2026-08-20'
        );

        $this->assertNotNull($order);
        $this->assertEquals(10, $order->quantity_produced);

        $fgInvAfter = InventoryItem::where('warehouse_id', $warehouse->id)
            ->where('item_type', 'finished_good')
            ->where('item_id', $product->id)
            ->value('quantity');

        $this->assertEquals($fgInvBefore + 10, $fgInvAfter);
    }
}
