<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $products = Product::where('company_id', $company->id)
            ->with(['rawMaterials', 'productionLines'])
            ->get();

        $rawMaterials = RawMaterial::all();

        return view('views.products', compact('company', 'products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'target_unit_cost' => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'selling_price' => $validated['selling_price'],
            'target_unit_cost' => $validated['target_unit_cost'],
            'quality_rating' => 88,
            'appeal_score' => 85,
            'status' => 'active',
        ]);

        return redirect()->route('products.index')->with('success', "Product {$product->name} created successfully!");
    }
}
