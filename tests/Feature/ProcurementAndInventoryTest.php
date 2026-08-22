<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\ProcurementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_order_creation_and_fulfillment(): void
    {
        $this->seed();
        $procurementService = app(ProcurementService::class);
        $company = Company::first();
        $supplier = Supplier::first();
        $warehouse = Warehouse::where('company_id', $company->id)->first();
        $material = RawMaterial::first();

        $initialCash = (float)$company->cash_balance;

        $po = $procurementService->createPurchaseOrder(
            company: $company,
            supplier: $supplier,
            warehouse: $warehouse,
            orderDate: '2026-08-20',
            items: [
                ['raw_material_id' => $material->id, 'quantity' => 100, 'unit_price' => 50.00]
            ]
        );

        $this->assertNotNull($po);
        $this->assertEquals('ordered', $po->status);
        $this->assertEquals(5000.00, $po->total_amount);

        // Verify cash deducted
        $company->refresh();
        $this->assertEquals($initialCash - 5000.00, (float)$company->cash_balance);

        // Fulfill PO
        $procurementService->fulfillPurchaseOrder($po, '2026-08-23');
        $this->assertEquals('delivered', $po->fresh()->status);

        // Verify stock added
        $inv = InventoryItem::where('warehouse_id', $warehouse->id)
            ->where('item_type', 'raw_material')
            ->where('item_id', $material->id)
            ->first();

        $this->assertNotNull($inv);
        $this->assertGreaterThanOrEqual(100, $inv->quantity);
    }
}
