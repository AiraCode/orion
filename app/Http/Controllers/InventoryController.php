<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $warehouses = Warehouse::where('company_id', $company->id)->with('inventoryItems')->get();
        $rawMaterialsItems = InventoryItem::where('company_id', $company->id)
            ->where('item_type', 'raw_material')
            ->with(['rawMaterial', 'warehouse'])
            ->get();

        $finishedGoodsItems = InventoryItem::where('company_id', $company->id)
            ->where('item_type', 'finished_good')
            ->with(['product', 'warehouse'])
            ->get();

        $valuation = $this->inventoryService->getInventoryValuation($company);
        $transactions = InventoryTransaction::where('company_id', $company->id)
            ->with('warehouse')
            ->latest('transaction_date')
            ->latest('id')
            ->take(50)
            ->get();

        return view('views.inventory', compact(
            'company',
            'warehouses',
            'rawMaterialsItems',
            'finishedGoodsItems',
            'valuation',
            'transactions'
        ));
    }
}
