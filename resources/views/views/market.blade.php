<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ tab: 0 }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Market Supremacy & Commercial Hub</h1>
                    <span class="badge badge-purple">{{ $industry->name }} Sector</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Competitive Intelligence & Customer Channels</p>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-sky-50 border border-sky-200">
                <span class="text-xs font-bold text-sky-800 uppercase tracking-wider">Total Industry TAM:</span>
                <span class="font-extrabold font-mono text-sm text-sky-700">${{ number_format($industry->market_size / 1000000000, 2) }}B</span>
            </div>
        </div>

        <!-- Market KPI Cards -->
        <div class="p-6 pb-0 flex-shrink-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">NovaTech Market Share</span>
                        <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                            <x-lucide-pie-chart class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-blue-600 mt-1">{{ $company->market_share_pct }}%</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Annual Industry CAGR</span>
                        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                            <x-lucide-trending-up class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-emerald-600 mt-1">+{{ $industry->growth_rate * 100 }}%</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Brand Authority Score</span>
                        <div class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center">
                            <x-lucide-sparkles class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-slate-900 mt-1">{{ $company->reputation_score }} <span class="text-sm font-semibold text-slate-400">/ 100</span></div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 px-6 pt-5 flex-shrink-0">
            @foreach(['Market Segments & Rivals', 'Sales Orders Fulfilled', 'Growth & Marketing Campaigns'] as $i => $tabName)
                <button
                    @click="tab = {{ $i }}"
                    class="px-4 py-2 text-xs font-bold rounded-xl cursor-pointer transition-all border"
                    :class="tab === {{ $i }} ? 'bg-white text-purple-700 border-slate-300 shadow-xs ring-2 ring-purple-500/20' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200'"
                >
                    {{ $tabName }}
                </button>
            @endforeach
        </div>

        <!-- Tab Contents -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- 1. Segments & Competitors -->
            <div x-show="tab === 0" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Segments -->
                    <div class="game-card p-5">
                        <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-3">Addressable Customer Segments</h3>
                        <div class="space-y-2.5">
                            @foreach($segments as $seg)
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="flex justify-between font-extrabold text-slate-900 mb-1">
                                        <span>{{ $seg->name }}</span>
                                        <span class="font-mono text-blue-600 font-extrabold">${{ number_format($seg->total_market_size_usd / 1000000, 0) }}M TAM</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 text-[11px] text-slate-500 font-mono mt-2 bg-white p-2 rounded-lg border border-slate-200">
                                        <div>Growth: <span class="text-emerald-600 font-bold">+{{ $seg->annual_growth_rate * 100 }}%</span></div>
                                        <div>Price Sens: <span class="text-slate-800 font-bold">{{ $seg->price_sensitivity }}%</span></div>
                                        <div>Quality Sens: <span class="text-sky-600 font-bold">{{ $seg->quality_sensitivity }}%</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Competitors -->
                    <div class="game-card p-5">
                        <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-3">Market Competitors & Rival Benchmark</h3>
                        <div class="space-y-2.5">
                            @foreach($competitors as $comp)
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div class="flex justify-between font-extrabold text-slate-900 mb-1">
                                        <span>{{ $comp->name }}</span>
                                        <span class="font-mono text-emerald-600 font-bold">{{ $comp->market_share_pct }}% Share</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-slate-500 font-mono mt-1">
                                        <span>Brand Rating: <strong class="text-slate-800">{{ $comp->brand_rating }}%</strong></span>
                                        <span>Price Index: <strong class="text-blue-600">{{ $comp->average_price_index }}x</strong></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Sales Orders -->
            <div x-show="tab === 1" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Fulfilled Customer Sales Orders</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Client Enterprise</th>
                                <th>Channel</th>
                                <th>Order Date</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">COGS</th>
                                <th class="text-right">Gross Margin</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @forelse($recentOrders as $order)
                                @php
                                    $margin = $order->total_amount > 0 ? (($order->total_amount - $order->total_cogs) / $order->total_amount) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="font-bold text-blue-600">#SO-{{ $order->id }}</td>
                                    <td class="font-sans font-bold text-slate-900">{{ $order->customer?->name }}</td>
                                    <td class="font-sans text-slate-600">{{ $order->salesChannel?->name }}</td>
                                    <td class="text-slate-600">{{ $order->order_date->format('Y-m-d') }}</td>
                                    <td class="text-right font-extrabold text-emerald-600">${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-right font-bold text-rose-500">${{ number_format($order->total_cogs, 2) }}</td>
                                    <td class="text-right font-extrabold text-sky-600">{{ number_format($margin, 1) }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-slate-400 font-sans">No customer sales orders processed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Marketing Campaigns -->
            <div x-show="tab === 2" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Active Growth & Marketing Campaigns</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($campaigns as $camp)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-900 font-heading">{{ $camp->name }}</h4>
                                    <span class="text-xs text-blue-600 uppercase font-mono font-bold">{{ $camp->channel }} Channel</span>
                                </div>
                                <span class="badge badge-green shadow-xs">
                                    {{ $camp->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 my-3 p-3 rounded-xl bg-white border border-slate-200 font-mono text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold">Daily Budget:</span>
                                    <div class="font-extrabold text-slate-900">${{ number_format($camp->daily_budget, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold">Total Spend:</span>
                                    <div class="font-extrabold text-rose-500">${{ number_format($camp->total_spend, 2) }}</div>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold">Audience Reach:</span>
                                    <div class="font-extrabold text-sky-600">{{ number_format($camp->reach) }}</div>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-500 uppercase font-bold">Leads Generated:</span>
                                    <div class="font-extrabold text-emerald-600">{{ number_format($camp->leads_generated) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
