<x-app-layout>
    @php
        $cashM = '$' . number_format($company->cash_balance / 1000000, 2) . 'M';
        $revM = '$' . number_format(($income['revenue'] ?? 0) / 1000000, 2) . 'M';
        $profitM = '$' . number_format(($income['net_profit'] ?? 0) / 1000000, 2) . 'M';
        $cogsM = '$' . number_format(($income['cogs'] ?? 0) / 1000000, 2) . 'M';
        $marginPct = ($income['gross_margin_pct'] ?? 0) . '%';
        $valM = '$' . number_format($company->valuation / 1000000, 0) . 'M';
        $marketShare = $company->market_share_pct . '%';
        $empCount = (string)$employeesCount;
    @endphp

    <div class="h-full overflow-y-auto p-6 space-y-6" x-data="dashboardData()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight font-heading">Executive Command Center</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                        Tycoon Live
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $company->name }} &middot; Q3 FY2026 &middot; Realtime Operational Metrics</p>
            </div>
            
            <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200 shadow-xs">
                <template x-for="p in timePeriods" :key="p">
                    <button @click="period = p"
                        class="px-3 h-7 rounded-lg text-xs font-bold transition-all cursor-pointer"
                        :class="period === p ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                        <span x-text="p"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <template x-for="kpi in kpis" :key="kpi.label">
                <div class="kpi-card group cursor-pointer">
                    <div class="flex items-start justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-xs" :class="kpi.iconBg">
                                <x-dynamic-component :component="'lucide-dollar-sign'" x-show="kpi.icon === 'dollar'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-trending-up'" x-show="kpi.icon === 'trend'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-award'" x-show="kpi.icon === 'award'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-pie-chart'" x-show="kpi.icon === 'pie'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-users'" x-show="kpi.icon === 'users'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-layers'" x-show="kpi.icon === 'layers'" class="w-3.5 h-3.5" />
                                <x-dynamic-component :component="'lucide-activity'" x-show="kpi.icon === 'activity'" class="w-3.5 h-3.5" />
                            </div>
                            <span class="text-xs font-bold text-slate-500" x-text="kpi.label"></span>
                        </div>
                        <span class="flex items-center gap-0.5 text-xs font-bold font-mono px-2 py-0.5 rounded-full"
                              :class="kpi.up ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-rose-700 bg-rose-50 border border-rose-200'">
                            <x-lucide-trending-up x-show="kpi.up" class="w-3 h-3" />
                            <x-lucide-trending-down x-show="!kpi.up" class="w-3 h-3" />
                            <span x-text="(kpi.up ? '+' : '') + kpi.change + '%'"></span>
                        </span>
                    </div>
                    <div class="font-extrabold font-mono text-xl text-slate-900 mb-1 tracking-tight" x-text="kpi.value"></div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[11px] font-semibold text-slate-400" x-text="'Prev: ' + kpi.prev"></span>
                        <span class="text-[10px] font-bold text-blue-600 group-hover:underline">Details &rarr;</span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Revenue / Profit chart -->
            <div class="lg:col-span-2 game-card p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <div class="text-sm font-bold text-slate-900 font-heading">Financial Trajectory</div>
                        <div class="text-xs text-slate-500 font-medium">Revenue, Operating Expenses & Net Earnings &mdash; FY2026</div>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold">
                        <div class="flex items-center gap-1.5 text-blue-600 bg-blue-50 px-2 py-1 rounded-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Revenue
                        </div>
                        <div class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Profit
                        </div>
                    </div>
                </div>
                <div id="financialChart" style="height: 230px;"></div>
            </div>

            <!-- Market share -->
            <div class="game-card p-5">
                <div class="mb-3 pb-3 border-b border-slate-100">
                    <div class="text-sm font-bold text-slate-900 font-heading">Market Supremacy</div>
                    <div class="text-xs text-slate-500 font-medium">Industry Share Q3 2026</div>
                </div>
                <div id="marketShareChart" style="height: 160px;"></div>
                <div class="space-y-2 mt-3 pt-2 border-t border-slate-100">
                    <template x-for="d in marketShareData" :key="d.name">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="'background: ' + d.color"></span>
                                <span class="font-bold text-slate-700" x-text="d.name"></span>
                            </div>
                            <span class="font-mono font-extrabold text-slate-900" x-text="d.value + '%'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Operations + Cash Flow Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-6">
            <!-- Operations -->
            <div class="game-card p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="text-sm font-bold text-slate-900 font-heading">Operations Health</div>
                    <span class="badge badge-green">98.2% Nominal</span>
                </div>
                <div class="space-y-4">
                    <template x-for="item in operations" :key="item.label">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold text-slate-700" x-text="item.label"></span>
                                <span class="font-mono text-xs font-extrabold" :class="item.warn ? 'text-amber-600' : 'text-slate-800'" x-text="item.val + '%'"></span>
                            </div>
                            <div class="h-2 rounded-full overflow-hidden bg-slate-100 border border-slate-200">
                                <div class="h-full rounded-full transition-all duration-500" 
                                     :class="item.warn ? 'bg-gradient-to-r from-amber-400 to-orange-500' : 'bg-gradient-to-r from-blue-500 to-indigo-600 animate-stripes'" 
                                     :style="'width: ' + item.val + '%'"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cash Flow Chart -->
            <div class="lg:col-span-2 game-card p-5">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <div class="text-sm font-bold text-slate-900 font-heading">Monthly Cash Flow Movement</div>
                        <div class="text-xs text-slate-500 font-medium">Operating, Capital Investment & Financing Flows</div>
                    </div>
                </div>
                <div id="cashFlowChart" style="height: 200px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardData', () => ({
                period: '1Y',
                timePeriods: ["7D", "30D", "3M", "1Y", "5Y"],
                kpis: [
                    { label: "Cash Balance", value: "{{ $cashM }}", prev: "$39.5M", change: 8.4, up: true, icon: 'dollar', iconBg: 'bg-emerald-500' },
                    { label: "Monthly Revenue", value: "{{ $revM }}", prev: "$4.31M", change: 14.2, up: true, icon: 'trend', iconBg: 'bg-blue-500' },
                    { label: "Net Profit", value: "{{ $profitM }}", prev: "$1.56M", change: 22.4, up: true, icon: 'award', iconBg: 'bg-indigo-500' },
                    { label: "COGS / Cost", value: "{{ $cogsM }}", prev: "$2.89M", change: 4.2, up: false, icon: 'layers', iconBg: 'bg-rose-500' },
                    { label: "Gross Margin", value: "{{ $marginPct }}", prev: "58.7%", change: 2.5, up: true, icon: 'pie', iconBg: 'bg-teal-500' },
                    { label: "Valuation", value: "{{ $valM }}", prev: "$240M", change: 18.3, up: true, icon: 'award', iconBg: 'bg-purple-500' },
                    { label: "Market Share", value: "{{ $marketShare }}", prev: "20.8%", change: 2.6, up: true, icon: 'activity', iconBg: 'bg-amber-500' },
                    { label: "Employees", value: "{{ $empCount }}", prev: "812", change: 4.3, up: true, icon: 'users', iconBg: 'bg-sky-500' },
                ],
                marketShareData: [
                    { name: 'NovaTech (You)', value: {{ $company->market_share_pct }}, color: '#3b82f6' },
                    { name: 'Quantum Corp', value: 18.1, color: '#8b5cf6' },
                    { name: 'Apex Systems', value: 15.2, color: '#ec4899' },
                    { name: 'Zenith Tech', value: 12.8, color: '#f59e0b' },
                    { name: 'Others', value: 30.5, color: '#94a3b8' },
                ],
                operations: [
                    { label: "Factory A Automation Efficiency", val: 91, warn: true },
                    { label: "Production Line Capacity", val: 84, warn: false },
                    { label: "Smart Warehouse Utilization", val: 67, warn: false },
                    { label: "Raw Material In-Stock Rate", val: 88, warn: false },
                    { label: "Supply Chain Resilience", val: 94, warn: false },
                ],
                init() {
                    // Financial Chart
                    new ApexCharts(document.querySelector("#financialChart"), {
                        series: [
                            { name: 'Revenue', data: [31, 40, 28, 51, 42, 109, 100] },
                            { name: 'Profit', data: [11, 32, 45, 32, 34, 52, 41] }
                        ],
                        chart: { type: 'area', height: 230, toolbar: { show: false }, background: 'transparent' },
                        colors: ['#3b82f6', '#10b981'],
                        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] } },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 3 },
                        xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], labels: { style: { colors: '#64748b', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 }, formatter: (val) => "$" + val + "M" } },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
                        theme: { mode: 'light' },
                        legend: { show: false }
                    }).render();

                    // Market Share Chart
                    new ApexCharts(document.querySelector("#marketShareChart"), {
                        series: [{{ $company->market_share_pct }}, 18.1, 15.2, 12.8, 30.5],
                        chart: { type: 'donut', height: 160, background: 'transparent' },
                        colors: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#94a3b8'],
                        labels: ['NovaTech', 'Quantum Corp', 'Apex Systems', 'Zenith Tech', 'Others'],
                        dataLabels: { enabled: false },
                        plotOptions: { pie: { donut: { size: '65%' } } },
                        stroke: { width: 3, colors: ['#ffffff'] },
                        theme: { mode: 'light' },
                        legend: { show: false }
                    }).render();

                    // Cash Flow Chart
                    new ApexCharts(document.querySelector("#cashFlowChart"), {
                        series: [
                            { name: 'Operating', data: [44, 55, 41, 67, 22, 43] },
                            { name: 'Investing', data: [-13, -23, -20, -8, -13, -27] },
                            { name: 'Financing', data: [11, -17, 15, 15, 21, -14] }
                        ],
                        chart: { type: 'bar', height: 200, stacked: true, toolbar: { show: false }, background: 'transparent' },
                        colors: ['#10b981', '#f43f5e', '#3b82f6'],
                        dataLabels: { enabled: false },
                        xaxis: { categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'], labels: { style: { colors: '#64748b', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 }, formatter: (val) => "$" + val + "M" } },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
                        theme: { mode: 'light' },
                        legend: { show: false },
                        plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } }
                    }).render();
                }
            }));
        });
    </script>
</x-app-layout>
