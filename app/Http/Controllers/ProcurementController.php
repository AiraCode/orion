<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\ProcurementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    public function __construct(protected ProcurementService $procurementService) {}

    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $suppliers = Supplier::with(['country', 'rawMaterials'])->get();
        $rawMaterials = RawMaterial::all();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $purchaseOrders = PurchaseOrder::where('company_id', $company->id)
            ->with(['supplier', 'warehouse', 'items.rawMaterial'])
            ->latest('order_date')
            ->latest('id')
            ->take(50)
            ->get();

        return view('views.procurement', compact('company', 'suppliers', 'rawMaterials', 'warehouses', 'purchaseOrders'));
    }

    public function order(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company->gameSession;

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
        $material = RawMaterial::findOrFail($validated['raw_material_id']);

        $supplierMat = $supplier->rawMaterials()->where('raw_material_id', $material->id)->first();
        $unitPrice = $supplierMat ? (float)$supplierMat->pivot->unit_price : (float)$material->base_cost;

        $this->procurementService->createPurchaseOrder(
            company: $company,
            supplier: $supplier,
            warehouse: $warehouse,
            orderDate: $session->current_game_date->toDateString(),
            items: [
                ['raw_material_id' => $material->id, 'quantity' => (int)$validated['quantity'], 'unit_price' => $unitPrice]
            ]
        );

        return redirect()->route('procurement.index')->with('success', "Purchase Order placed for {$validated['quantity']}x {$material->name}!");
    }
}
