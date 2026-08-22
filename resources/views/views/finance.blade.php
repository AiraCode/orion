<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ tab: 0 }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Financial Core & General Ledger</h1>
                    <span class="badge badge-green">GAAP Double-Entry</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Real-time Automated Accounting & P&L Statement</p>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-bold text-emerald-800">
                    Ledger Status: {{ $balance['is_balanced'] ? 'BALANCED (Assets = Liab + Equity)' : 'CHECKING' }}
                </span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 px-6 pt-5 flex-shrink-0">
            @foreach(['Income Statement (P&L)', 'Balance Sheet', 'Chart of Accounts', 'Journal Audit Ledger'] as $i => $tabName)
                <button
                    @click="tab = {{ $i }}"
                    class="px-4 py-2 text-xs font-bold rounded-xl cursor-pointer transition-all border"
                    :class="tab === {{ $i }} ? 'bg-white text-emerald-700 border-slate-300 shadow-xs ring-2 ring-emerald-500/20' : 'bg-slate-100 text-slate-600 border-transparent hover:bg-slate-200'"
                >
                    {{ $tabName }}
                </button>
            @endforeach
        </div>

        <!-- Tab Contents -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- 1. Income Statement -->
            <div x-show="tab === 0" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="kpi-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Revenue</span>
                            <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                                <x-lucide-arrow-up-right class="w-3.5 h-3.5" />
                            </div>
                        </div>
                        <div class="text-2xl font-extrabold font-mono text-slate-900 mt-1">${{ number_format($income['revenue'], 2) }}</div>
                    </div>
                    <div class="kpi-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Cost of Goods Sold</span>
                            <div class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center">
                                <x-lucide-arrow-down-right class="w-3.5 h-3.5" />
                            </div>
                        </div>
                        <div class="text-2xl font-extrabold font-mono text-rose-500 mt-1">${{ number_format($income['cogs'], 2) }}</div>
                    </div>
                    <div class="kpi-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Gross Margin</span>
                            <div class="w-6 h-6 rounded-lg bg-teal-500 text-white flex items-center justify-center">
                                <x-lucide-pie-chart class="w-3.5 h-3.5" />
                            </div>
                        </div>
                        <div class="text-2xl font-extrabold font-mono text-teal-600 mt-1">{{ $income['gross_margin_pct'] }}%</div>
                    </div>
                    <div class="kpi-card">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Net Operating Profit</span>
                            <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                                <x-lucide-award class="w-3.5 h-3.5" />
                            </div>
                        </div>
                        <div class="text-2xl font-extrabold font-mono {{ $income['net_profit'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }} mt-1">
                            ${{ number_format($income['net_profit'], 2) }}
                        </div>
                    </div>
                </div>

                <div class="game-card p-5">
                    <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Income Statement Breakdown</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Account Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Total Balance</th>
                                </tr>
                            </thead>
                            <tbody class="font-mono">
                                <tr class="text-emerald-700 font-bold bg-emerald-50/40">
                                    <td class="py-3 font-sans">Operating Revenue (4000 series)</td>
                                    <td class="py-3 text-right text-slate-400">-</td>
                                    <td class="py-3 text-right">${{ number_format($income['revenue'], 2) }}</td>
                                    <td class="py-3 text-right font-extrabold text-emerald-700">${{ number_format($income['revenue'], 2) }}</td>
                                </tr>
                                <tr class="text-rose-600">
                                    <td class="py-2.5 pl-6 font-sans">&bull; Cost of Goods Sold (5000 series)</td>
                                    <td class="py-2.5 text-right">${{ number_format($income['cogs'], 2) }}</td>
                                    <td class="py-2.5 text-right text-slate-400">-</td>
                                    <td class="py-2.5 text-right">(${{ number_format($income['cogs'], 2) }})</td>
                                </tr>
                                <tr class="text-slate-900 font-extrabold bg-slate-100/70 border-t border-b border-slate-200">
                                    <td class="py-3 font-sans">GROSS PROFIT</td>
                                    <td></td><td></td>
                                    <td class="py-3 text-right text-emerald-700 text-sm font-extrabold">${{ number_format($income['gross_profit'], 2) }}</td>
                                </tr>
                                @foreach($income['accounts_breakdown']['operating_expenses'] as $opExp)
                                    <tr class="text-slate-600">
                                        <td class="py-2.5 pl-6 font-sans">&bull; {{ $opExp->code }} - {{ $opExp->name }}</td>
                                        <td class="py-2.5 text-right">${{ number_format($opExp->balance, 2) }}</td>
                                        <td class="py-2.5 text-right text-slate-400">-</td>
                                        <td class="py-2.5 text-right">(${{ number_format($opExp->balance, 2) }})</td>
                                    </tr>
                                @endforeach
                                <tr class="text-slate-900 font-extrabold text-sm bg-blue-50/80 border-t-2 border-blue-200">
                                    <td class="py-3.5 font-sans font-heading text-blue-900">NET OPERATING PROFIT</td>
                                    <td></td><td></td>
                                    <td class="py-3.5 text-right text-emerald-700 font-extrabold text-base">${{ number_format($income['net_profit'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Balance Sheet -->
            <div x-show="tab === 1" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Assets -->
                    <div class="game-card p-5">
                        <h3 class="text-sm font-extrabold text-blue-700 font-heading mb-4 flex items-center gap-2">
                            <x-lucide-shield class="w-4 h-4 text-blue-600" />
                            ASSETS (1000 - 1999)
                        </h3>
                        <table class="w-full text-xs font-mono">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Current Assets (Cash, Inventory, Receivables)</td>
                                    <td class="py-2.5 text-right font-extrabold text-slate-900">${{ number_format($balance['current_assets'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Non-Current Assets (Plants & Equipment)</td>
                                    <td class="py-2.5 text-right font-extrabold text-slate-900">${{ number_format($balance['non_current_assets'], 2) }}</td>
                                </tr>
                                <tr class="bg-blue-50/80 border-t-2 border-blue-200 font-extrabold">
                                    <td class="py-3 font-sans text-blue-900">TOTAL ASSETS</td>
                                    <td class="py-3 text-right text-blue-700 text-sm">${{ number_format($balance['total_assets'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Liabilities & Equity -->
                    <div class="game-card p-5">
                        <h3 class="text-sm font-extrabold text-indigo-700 font-heading mb-4 flex items-center gap-2">
                            <x-lucide-layers class="w-4 h-4 text-indigo-600" />
                            LIABILITIES & EQUITY (2000 - 3999)
                        </h3>
                        <table class="w-full text-xs font-mono">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Current Liabilities (Payables & Tax)</td>
                                    <td class="py-2.5 text-right font-extrabold text-slate-900">${{ number_format($balance['current_liabilities'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Long-Term Liabilities (Corporate Debt)</td>
                                    <td class="py-2.5 text-right font-extrabold text-slate-900">${{ number_format($balance['long_term_liabilities'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Paid-in Capital (Shareholder Equity)</td>
                                    <td class="py-2.5 text-right font-extrabold text-slate-900">${{ number_format($balance['equity'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-slate-600 font-sans">Current Period Retained Earnings</td>
                                    <td class="py-2.5 text-right font-extrabold text-emerald-600">${{ number_format($balance['retained_earnings_current'], 2) }}</td>
                                </tr>
                                <tr class="bg-indigo-50/80 border-t-2 border-indigo-200 font-extrabold">
                                    <td class="py-3 font-sans text-indigo-900">TOTAL LIABILITIES & EQUITY</td>
                                    <td class="py-3 text-right text-indigo-700 text-sm">${{ number_format($balance['total_liabilities_and_equity'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Chart of Accounts -->
            <div x-show="tab === 2" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Chart of Accounts (General Ledger Setup)</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Account Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Subtype</th>
                                <th class="text-right">Current Balance</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @foreach($accounts as $acc)
                                <tr>
                                    <td class="text-blue-600 font-extrabold">{{ $acc->code }}</td>
                                    <td class="text-slate-900 font-sans font-bold">{{ $acc->name }}</td>
                                    <td class="text-slate-600 uppercase font-sans text-[11px]">{{ $acc->type }}</td>
                                    <td class="text-slate-500 font-sans text-[11px]">{{ $acc->subtype }}</td>
                                    <td class="text-right font-extrabold {{ $acc->balance >= 0 ? 'text-slate-900' : 'text-rose-500' }}">
                                        ${{ number_format($acc->balance, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Journal Entries -->
            <div x-show="tab === 3" class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Posted Journal Entries (Double-Entry Audit Log)</h3>
                <div class="space-y-3">
                    @foreach($journalEntries as $entry)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">#{{ $entry->id }}</span>
                                    <span class="text-xs text-slate-800 font-bold font-sans">{{ $entry->description }}</span>
                                    <span class="badge badge-blue">{{ $entry->reference_type ?? 'GENERAL' }}</span>
                                </div>
                                <span class="text-xs text-slate-500 font-mono font-semibold">{{ $entry->entry_date->format('Y-m-d') }}</span>
                            </div>
                            <table class="w-full text-xs font-mono bg-white rounded-lg p-2 border border-slate-100">
                                <tbody>
                                    @foreach($entry->lines as $line)
                                        <tr class="text-slate-700">
                                            <td class="py-1.5 pl-2 {{ $line->type === 'credit' ? 'pl-8' : '' }}">
                                                <span class="font-bold text-slate-900">{{ $line->account->code }}</span> - {{ $line->account->name }} ({{ $line->memo ?? '' }})
                                            </td>
                                            <td class="py-1.5 text-right font-bold text-sky-600">
                                                {{ $line->type === 'debit' ? '$' . number_format($line->amount, 2) : '-' }}
                                            </td>
                                            <td class="py-1.5 text-right font-bold text-indigo-600 pr-2">
                                                {{ $line->type === 'credit' ? '$' . number_format($line->amount, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
