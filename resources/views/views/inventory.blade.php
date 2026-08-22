<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ tab: 0 }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Inventory & Warehousing Depot</h1>
                    <span class="badge badge-cyan">Real-time Logistics</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Real-time Stock Levels & Warehouse Valuation</p>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Total Value:</span>
                <span class="font-extrabold font-mono text-sm text-emerald-700">${{ number_format($valuation['total_inventory_value'], 2) }}</span>
            </div>
        </div>

        <!-- Inventory KPI Cards -->
        <div class="p-6 pb-0 flex-shrink-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Raw Materials Value</span>
                        <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                            <x-lucide-box class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-xl font-extrabold font-mono text-blue-600 mt-1">${{ number_format($valuation['raw_materials_value'], 2) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Finished Goods Value</span>
                        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                            <x-lucide-package-check class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-xl font-extrabold font-mono text-emerald-600 mt-1">${{ number_format($valuation['finished_goods_value'], 2) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Active Warehouses</span>
                        <div class="w-6 h-6 rounded-lg bg-indigo-500 text-white flex items-center justify-center">
                            <x-lucide-warehouse class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-xl font-extrabold font-mono text-indigo-600 mt-1">{{ $warehouses->count() }} Facilities</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 px-6 pt-5 flex-shrink-0">
            @foreach(['Raw Materials Stock', 'Finished Goods Stock', 'Inventory Movement Ledger'] as $i => $tabName)
                <button
                    @click="tab = {{ $i }}"
                    class="px-4 py-2 text-xs font-bold rounded-xl cursor-pointer transition-all border"
                    :class="tab === {{ $i }} ? 'bg-white text-blue-700 border-slate-300 shadow-xs ring-2 ring-blue-500/20' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200'"
                >
                    {{ $tabName }}
                </button>
            @endforeach
        </div>

        <!-- Tab Contents -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <!-- 1. Raw Materials -->
            <div x-show="tab === 0" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Raw Materials on Hand</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Material Name</th>
                                <th>Category</th>
                                <th>Warehouse</th>
                                <th class="text-right">In Stock Qty</th>
                                <th class="text-right">Avg Unit Cost</th>
                                <th class="text-right">Total Value</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($rawMaterialsItems as $item)
                                <tr>
                                    <td class="text-blue-600 font-bold">{{ $item->rawMaterial?->sku }}</td>
                                    <td class="font-sans font-bold text-slate-900">{{ $item->rawMaterial?->name }}</td>
                                    <td class="font-sans text-slate-600">{{ $item->rawMaterial?->category }}</td>
                                    <td class="font-sans text-slate-600">{{ $item->warehouse?->name }}</td>
                                    <td class="text-right font-extrabold text-sky-600">{{ number_format($item->quantity) }} {{ $item->rawMaterial?->unit }}</td>
                                    <td class="text-right text-slate-600">${{ number_format($item->average_unit_cost, 2) }}</td>
                                    <td class="text-right font-extrabold text-emerald-600">${{ number_format($item->total_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No raw material stock in warehouse.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Finished Goods -->
            <div x-show="tab === 1" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Finished Products Ready for Fulfillment</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Warehouse</th>
                                <th class="text-right">Available Inventory</th>
                                <th class="text-right">Avg Unit Cost</th>
                                <th class="text-right">Selling Price</th>
                                <th class="text-right">Total Inventory Value</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($finishedGoodsItems as $item)
                                <tr>
                                    <td class="text-blue-600 font-bold">{{ $item->product?->sku }}</td>
                                    <td class="font-sans font-extrabold text-slate-900">{{ $item->product?->name }}</td>
                                    <td class="font-sans text-slate-600">{{ $item->warehouse?->name }}</td>
                                    <td class="text-right font-extrabold text-emerald-600">{{ number_format($item->quantity) }} units</td>
                                    <td class="text-right text-slate-600">${{ number_format($item->average_unit_cost, 2) }}</td>
                                    <td class="text-right text-slate-900 font-bold">${{ number_format($item->product?->selling_price, 2) }}</td>
                                    <td class="text-right font-extrabold text-emerald-600">${{ number_format($item->total_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No finished goods in inventory.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Transactions Log -->
            <div x-show="tab === 2" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Warehouse Movement Ledger</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Warehouse</th>
                                <th>Item Type</th>
                                <th>Movement Type</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($transactions as $txn)
                                <tr>
                                    <td class="text-slate-600">{{ $txn->transaction_date->format('Y-m-d') }}</td>
                                    <td class="font-sans font-bold text-slate-800">{{ $txn->warehouse?->name }}</td>
                                    <td class="uppercase text-[10px] text-slate-500 font-bold">{{ str_replace('_', ' ', $txn->item_type) }}</td>
                                    <td class="font-sans">
                                        <span class="badge {{ in_array($txn->transaction_type, ['purchase_in', 'production_in']) ? 'badge-green' : 'badge-red' }}">
                                            {{ str_replace('_', ' ', $txn->transaction_type) }}
                                        </span>
                                    </td>
                                    <td class="text-right font-extrabold {{ $txn->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $txn->quantity > 0 ? '+' : '' }}{{ number_format($txn->quantity) }}
                                    </td>
                                    <td class="text-right text-slate-600">${{ number_format($txn->unit_cost, 2) }}</td>
                                    <td class="text-right font-bold text-slate-900">${{ number_format($txn->total_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No inventory movements recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
