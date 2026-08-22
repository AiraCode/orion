<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ prodModal: false, selectedLine: {{ $productionLines->first()?->id ?? 'null' }}, selectedProduct: {{ $products->first()?->id ?? 'null' }}, selectedWarehouse: {{ $warehouses->first()?->id ?? 'null' }} }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Manufacturing & Assembly Lines</h1>
                    <span class="badge badge-yellow">Robotics & Industrial</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Automated Assembly Lines & Production Batches</p>
            </div>
            <button
                @click="prodModal = true"
                class="btn-game-amber px-4 py-2 text-xs flex items-center gap-2 cursor-pointer"
            >
                <x-lucide-factory class="w-4 h-4" />
                <span>Start Production Run</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Facilities Overview -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 font-heading">Operating Facilities & Plants</h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $facilities->count() }} active mega-factories</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($facilities as $fac)
                        <div class="game-card p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h4 class="text-base font-extrabold text-slate-900 font-heading">{{ $fac->name }}</h4>
                                        <span class="text-[11px] text-slate-400 uppercase font-mono font-bold">{{ $fac->type }}</span>
                                    </div>
                                    <span class="badge badge-green shadow-xs">
                                        {{ $fac->status }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-3 my-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-mono">
                                    <div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Condition</div>
                                        <div class="font-extrabold text-slate-900 text-sm">{{ $fac->building_condition }}%</div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Efficiency</div>
                                        <div class="font-extrabold text-emerald-600 text-sm">{{ $fac->efficiency_rating }}%</div>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Op. Cost</div>
                                        <div class="font-extrabold text-rose-500 text-sm">${{ number_format($fac->monthly_operating_cost / 1000, 0) }}k/mo</div>
                                    </div>
                                </div>

                                <!-- Production lines in this facility -->
                                <div class="border-t border-slate-100 pt-3">
                                    <div class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 mb-2">Assigned Production Lines</div>
                                    <div class="space-y-2">
                                        @foreach($fac->productionLines as $line)
                                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                                <div class="flex justify-between items-center mb-1.5">
                                                    <span class="font-bold text-slate-800">{{ $line->name }}</span>
                                                    <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-[11px]">{{ $line->daily_capacity_units }} units/day</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] text-slate-500 font-medium">Utilization:</span>
                                                    <div class="flex-1 h-2 rounded-full bg-slate-200 overflow-hidden">
                                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 animate-stripes" style="width: {{ $line->current_utilization_pct }}%"></div>
                                                    </div>
                                                    <span class="text-[11px] font-mono text-slate-800 font-extrabold">{{ $line->current_utilization_pct }}%</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Production Orders Log -->
            <div class="game-card p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 font-heading">Completed Production Runs & Unit Cost Analytics</h3>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Product Manufactured</th>
                                <th>Assembly Line</th>
                                <th class="text-right">Quantity Produced</th>
                                <th class="text-right">Total Material Cost</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($productionOrders as $pOrder)
                                <tr>
                                    <td class="font-bold text-blue-600">#PROD-{{ $pOrder->id }}</td>
                                    <td class="font-sans font-bold text-slate-900">{{ $pOrder->product->name }}</td>
                                    <td class="font-sans text-slate-600">{{ $pOrder->productionLine->name }}</td>
                                    <td class="text-right text-emerald-600 font-extrabold">{{ number_format($pOrder->quantity_produced) }} units</td>
                                    <td class="text-right font-bold text-slate-900">${{ number_format($pOrder->total_production_cost, 2) }}</td>
                                    <td class="text-right text-sky-600 font-bold">${{ number_format($pOrder->quantity_produced > 0 ? $pOrder->total_production_cost / $pOrder->quantity_produced : 0, 2) }}</td>
                                    <td class="text-right font-sans">
                                        <span class="badge badge-green">
                                            {{ strtoupper($pOrder->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No production runs executed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Production Modal -->
        <div x-show="prodModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs" style="display: none;">
            <div class="w-full max-w-md p-6 rounded-2xl border border-slate-200 bg-white shadow-2xl animate-pop" @click.outside="prodModal = false">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 font-heading">Execute Assembly Run</h3>
                    <button @click="prodModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <form action="{{ route('production.produce') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Production Line</label>
                        <select name="production_line_id" x-model="selectedLine" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($productionLines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }} ({{ $line->facility->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Product to Assemble</label>
                        <select name="product_id" x-model="selectedProduct" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Target Cost: ${{ number_format($prod->target_unit_cost, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Source & Destination Warehouse</label>
                        <select name="warehouse_id" x-model="selectedWarehouse" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batch Size (Units)</label>
                        <input type="number" min="1" max="1000" name="quantity" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none" value="50">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="prodModal = false" class="px-4 h-9 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all">Cancel</button>
                        <button type="submit" class="btn-game-amber px-5 h-9 text-xs cursor-pointer">Run Production</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
