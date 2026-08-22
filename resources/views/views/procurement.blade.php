<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ poModal: false, selectedSupplier: {{ $suppliers->first()?->id ?? 'null' }}, selectedWarehouse: {{ $warehouses->first()?->id ?? 'null' }} }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Procurement & Supply Chain</h1>
                    <span class="badge badge-amber">Global Logistics</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Global Supplier Network & Raw Material Sourcing</p>
            </div>
            <button
                @click="poModal = true"
                class="btn-game-amber px-4 py-2 text-xs flex items-center gap-2 cursor-pointer"
            >
                <x-lucide-shopping-cart class="w-4 h-4" />
                <span>Issue Purchase Order</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Suppliers Grid -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 font-heading">Qualified Global Suppliers</h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $suppliers->count() }} active partners</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($suppliers as $sup)
                        <div class="game-card p-4 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="text-sm font-bold text-slate-900 font-heading">{{ $sup->name }}</h4>
                                    <span class="badge badge-blue">{{ $sup->country?->name ?? 'Global' }}</span>
                                </div>
                                <div class="space-y-2 text-xs text-slate-600 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Lead Time:</span>
                                        <span class="font-mono text-blue-600 font-bold">{{ $sup->lead_time_days }} days</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Quality Rating:</span>
                                        <span class="font-mono text-emerald-600 font-bold">{{ $sup->quality_rating }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-medium">Reliability:</span>
                                        <span class="font-mono text-sky-600 font-bold">{{ $sup->reliability_rating }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 pt-2 text-[11px]">
                                <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Supplied Materials:</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($sup->rawMaterials as $mat)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-mono font-bold border border-slate-200">
                                            {{ $mat->name }} (${{ number_format($mat->pivot->unit_price, 2) }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Purchase Orders Table -->
            <div class="game-card p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 font-heading">Purchase Orders History & In-Transit Shipments</h3>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Supplier</th>
                                <th>Destination Warehouse</th>
                                <th>Order Date</th>
                                <th>Expected Delivery</th>
                                <th class="text-right">Total Cost</th>
                                <th class="text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($purchaseOrders as $po)
                                <tr>
                                    <td class="font-bold text-blue-600">#PO-{{ $po->id }}</td>
                                    <td class="font-sans font-bold text-slate-900">{{ $po->supplier->name }}</td>
                                    <td class="font-sans text-slate-600">{{ $po->warehouse?->name ?? 'Central Hub' }}</td>
                                    <td class="text-slate-600">{{ $po->order_date->format('Y-m-d') }}</td>
                                    <td class="text-sky-600 font-bold">{{ $po->expected_delivery_date->format('Y-m-d') }}</td>
                                    <td class="text-right font-extrabold text-slate-900">${{ number_format($po->total_amount, 2) }}</td>
                                    <td class="text-right font-sans">
                                        <span class="badge {{ $po->status === 'delivered' ? 'badge-green' : 'badge-yellow' }}">
                                            {{ strtoupper($po->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No purchase orders placed yet. Click "Issue Purchase Order" to start sourcing.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Issue PO Modal -->
        <div x-show="poModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs" style="display: none;">
            <div class="w-full max-w-md p-6 rounded-2xl border border-slate-200 bg-white shadow-2xl animate-pop" @click.outside="poModal = false">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 font-heading">Issue Raw Material PO</h3>
                    <button @click="poModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <form action="{{ route('procurement.order') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select Supplier</label>
                        <select name="supplier_id" x-model="selectedSupplier" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }} (Lead: {{ $sup->lead_time_days }}d, Rating: {{ $sup->quality_rating }}%)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Destination Warehouse</label>
                        <select name="warehouse_id" x-model="selectedWarehouse" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }} (Capacity: {{ number_format($wh->current_capacity_units) }} / {{ number_format($wh->max_capacity_units) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Raw Material</label>
                        <select name="raw_material_id" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($rawMaterials as $mat)
                                <option value="{{ $mat->id }}">{{ $mat->name }} (Base: ${{ number_format($mat->base_cost, 2) }}/{{ $mat->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Order Quantity (Units)</label>
                        <input type="number" min="10" name="quantity" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none" value="500">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="poModal = false" class="px-4 h-9 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all">Cancel</button>
                        <button type="submit" class="btn-game-amber px-5 h-9 text-xs cursor-pointer">Submit Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
