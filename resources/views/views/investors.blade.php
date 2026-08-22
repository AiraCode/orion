<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ tab: 0 }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Cap Table, Investors & Strategic Assets</h1>
                    <span class="badge badge-purple">Capital Structure</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Shareholder Equity & Treasury Investments</p>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-purple-50 border border-purple-200 shadow-xs">
                <span class="text-xs font-bold text-purple-800 uppercase tracking-wider">Enterprise Valuation:</span>
                <span class="font-extrabold font-mono text-sm text-purple-700">${{ number_format($valuation / 1000000, 1) }}M</span>
            </div>
        </div>

        <!-- Cap Table KPIs -->
        <div class="p-6 pb-0 flex-shrink-0">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Shares Outstanding</span>
                        <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                            <x-lucide-pie-chart class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-slate-900 mt-1">{{ number_format($totalShares) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Share Price (Implied)</span>
                        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                            <x-lucide-trending-up class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-emerald-600 mt-1">${{ number_format($sharePrice, 2) }} <span class="text-xs text-slate-400 font-sans">/ share</span></div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Treasury Investments</span>
                        <div class="w-6 h-6 rounded-lg bg-purple-500 text-white flex items-center justify-center">
                            <x-lucide-award class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-purple-600 mt-1">${{ number_format($investments->sum('current_value') / 1000000, 1) }}M</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 px-6 pt-5 flex-shrink-0">
            @foreach(['Capitalization Table (Cap Table)', 'Treasury & Liquid Yield Assets'] as $i => $tabName)
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
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- 1. Cap Table -->
            <div x-show="tab === 0" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Ownership Distribution & Voting Rights</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Shareholder Name</th>
                                <th>Investor Class</th>
                                <th class="text-right">Shares Held</th>
                                <th class="text-right">Ownership %</th>
                                <th class="text-right">Total Invested</th>
                                <th class="text-right">Current Value</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @foreach($shareholders as $sh)
                                @php
                                    $shVal = ($sh->ownership_percentage / 100) * $valuation;
                                @endphp
                                <tr>
                                    <td class="font-sans font-extrabold text-slate-900">{{ $sh->name }}</td>
                                    <td class="font-sans">
                                        <span class="badge badge-purple">{{ $sh->type }}</span>
                                    </td>
                                    <td class="text-right font-bold text-slate-700">{{ number_format($sh->share_count) }}</td>
                                    <td class="text-right font-extrabold text-emerald-600">{{ number_format($sh->ownership_percentage, 2) }}%</td>
                                    <td class="text-right text-slate-600">${{ number_format($sh->total_invested, 2) }}</td>
                                    <td class="text-right font-extrabold text-purple-700">${{ number_format($shVal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Treasury Investments -->
            <div x-show="tab === 1" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Corporate Yield Assets & High-Grade Bonds</h3>
                <div class="space-y-3">
                    @forelse($investments as $inv)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex justify-between items-center text-xs">
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-sm font-heading">{{ $inv->name }}</h4>
                                <span class="badge badge-blue mt-1 uppercase">{{ str_replace('_', ' ', $inv->type) }}</span>
                            </div>
                            <div class="grid grid-cols-3 gap-6 text-right font-mono">
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold">Principal</div>
                                    <div class="text-slate-800 font-extrabold">${{ number_format($inv->principal_amount, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold">Annual Yield</div>
                                    <div class="text-emerald-600 font-extrabold">+{{ number_format($inv->annual_return_rate * 100, 2) }}%</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold">Market Value</div>
                                    <div class="text-purple-700 font-extrabold text-sm">${{ number_format($inv->current_value, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs font-sans">
                            No treasury investments currently held.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
