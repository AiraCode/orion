<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Facility;
use App\Models\Product;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use App\Services\ProductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function __construct(protected ProductionService $productionService) {}

    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $facilities = Facility::where('company_id', $company->id)->with('productionLines.product')->get();
        $productionLines = ProductionLine::whereHas('facility', fn($q) => $q->where('company_id', $company->id))->with(['facility', 'product'])->get();
        $products = Product::where('company_id', $company->id)->with('rawMaterials')->get();
        $warehouses = Warehouse::where('company_id', $company->id)->get();
        $productionOrders = ProductionOrder::where('company_id', $company->id)->with(['productionLine', 'product'])->latest()->take(50)->get();

        return view('views.production', compact('company', 'facilities', 'productionLines', 'products', 'warehouses', 'productionOrders'));
    }

    public function produce(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company->gameSession;

        $validated = $request->validate([
            'production_line_id' => 'required|exists:production_lines,id',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $line = ProductionLine::findOrFail($validated['production_line_id']);
        $product = Product::findOrFail($validated['product_id']);
        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

        $this->productionService->createProductionOrder(
            company: $company,
            productionLine: $line,
            product: $product,
            warehouse: $warehouse,
            quantity: (int)$validated['quantity'],
            startDate: $session->current_game_date->toDateString()
        );

        return redirect()->route('production.index')->with('success', "Production run completed for {$validated['quantity']}x {$product->name}!");
    }
}
