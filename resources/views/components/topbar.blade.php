@php
    $user = Auth::user();
    $company = $user ? ($user->activeCompany() ?? \App\Models\Company::first()) : null;
    $session = $company ? ($company->gameSession ?? \App\Models\GameSession::first()) : null;
    $unreadAlertsCount = $company ? \App\Models\GameAlert::where('company_id', $company->id)->where('is_read', false)->count() : 0;
    $cashFmt = $company ? '$' . number_format($company->cash_balance / 1000000, 1) . 'M' : '$0.0M';
    $valFmt = $company ? '$' . number_format($company->valuation / 1000000, 0) . 'M' : '$0M';
    $currentDate = $session ? $session->current_game_date->format('M d, Y') : date('M d, Y');
@endphp

<div class="flex items-center h-14 px-4 gap-3 select-none flex-shrink-0 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-xs z-30"
     x-data="{
        currentDate: '{{ $currentDate }}',
        cash: '{{ $cashFmt }}',
        valuation: '{{ $valFmt }}',
        speed: '{{ $session?->speed ?? '1x' }}',
        loading: false,
        cashUpdated: false,
        async advance(days) {
            this.loading = true;
            try {
                const res = await fetch('{{ route('simulation.advance') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ days })
                });
                const data = await res.json();
                if (data.success) {
                    const d = new Date(data.current_date);
                    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    this.currentDate = months[d.getUTCMonth()] + ' ' + String(d.getUTCDate()).padStart(2, '0') + ', ' + d.getUTCFullYear();
                    this.cash = '$' + (data.cash_balance / 1000000).toFixed(1) + 'M';
                    this.valuation = '$' + (data.valuation / 1000000).toFixed(0) + 'M';
                    
                    // Trigger pulse animation
                    this.cashUpdated = true;
                    setTimeout(() => this.cashUpdated = false, 1200);

                    // Trigger custom event for other Alpine components to refresh if needed
                    window.dispatchEvent(new CustomEvent('simulation-ticked', { detail: data }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        async setSpeed(s) {
            this.speed = s;
            await fetch('{{ route('simulation.speed') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ speed: s })
            });
        }
     }">
    <!-- Brand -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 flex-shrink-0 group hover:opacity-95 transition-all">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 shadow-md shadow-blue-500/30 group-hover:scale-105 transition-transform">
            <span class="text-white font-extrabold text-sm tracking-wider drop-shadow-sm font-heading">O</span>
        </div>
        <div>
            <div class="font-extrabold text-base tracking-wider bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent font-heading">ORION</div>
        </div>
    </a>

    <div class="w-px h-7 flex-shrink-0 bg-slate-200 mx-1"></div>

    <!-- Company Info & Date -->
    <div class="flex flex-col flex-shrink-0">
        <span class="text-slate-800 font-bold text-xs leading-tight flex items-center gap-1.5">
            {{ $company?->name ?? 'ORION Enterprise' }}
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        </span>
        <span class="text-xs leading-tight font-mono font-semibold text-blue-600 flex items-center gap-1">
            <x-lucide-calendar class="w-3 h-3 text-blue-500" />
            <span x-text="currentDate"></span>
        </span>
    </div>

    <div class="flex-1"></div>

    <!-- Live KPI Game Badges -->
    <div class="hidden lg:flex items-center gap-3">
        <!-- Cash Pill -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 shadow-xs transition-all"
             :class="{ 'ring-2 ring-emerald-400 scale-105 bg-emerald-100': cashUpdated }">
            <div class="w-5 h-5 rounded-lg bg-emerald-500 text-white flex items-center justify-center shadow-xs">
                <x-lucide-dollar-sign class="w-3.5 h-3.5" />
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-emerald-800 text-[9px] uppercase tracking-wider">Cash Treasury</span>
                <span class="font-extrabold font-mono text-emerald-700 text-xs" x-text="cash"></span>
            </div>
            <span class="flex items-center gap-0.5 font-bold font-mono text-[10px] text-emerald-600 bg-emerald-100/80 px-1.5 py-0.5 rounded-md">
                <x-lucide-trending-up class="w-2.5 h-2.5" /> +8.4%
            </span>
        </div>

        <!-- Valuation Pill -->
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200/80 shadow-xs transition-all"
             :class="{ 'ring-2 ring-indigo-400 scale-105 bg-indigo-100': cashUpdated }">
            <div class="w-5 h-5 rounded-lg bg-indigo-500 text-white flex items-center justify-center shadow-xs">
                <x-lucide-award class="w-3.5 h-3.5" />
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-indigo-800 text-[9px] uppercase tracking-wider">Valuation</span>
                <span class="font-extrabold font-mono text-indigo-700 text-xs" x-text="valuation"></span>
            </div>
            <span class="flex items-center gap-0.5 font-bold font-mono text-[10px] text-indigo-600 bg-indigo-100/80 px-1.5 py-0.5 rounded-md">
                <x-lucide-trending-up class="w-2.5 h-2.5" /> +18.3%
            </span>
        </div>
    </div>

    <div class="flex-1 hidden lg:block"></div>

    <!-- Time Advance Actions -->
    <div class="flex items-center gap-2 flex-shrink-0">
        <button
            @click="advance(1)"
            :disabled="loading"
            class="px-3 h-8 rounded-xl text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white shadow-sm shadow-blue-500/20 active:scale-95 disabled:opacity-50"
            title="Advance 1 Game Day"
        >
            <x-lucide-fast-forward class="w-3.5 h-3.5" />
            <span>+1 Day</span>
        </button>

        <button
            @click="advance(7)"
            :disabled="loading"
            class="px-3 h-8 rounded-xl text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 text-white shadow-sm shadow-indigo-500/20 active:scale-95 disabled:opacity-50"
            title="Advance 7 Game Days"
        >
            <x-lucide-fast-forward class="w-3.5 h-3.5" />
            <span>+7 Days</span>
        </button>
    </div>

    <!-- Simulation Speed Selector -->
    <div class="flex items-center gap-1 flex-shrink-0 bg-slate-100 border border-slate-200/80 rounded-xl p-1 shadow-inner">
        <button
            @click="setSpeed('paused')"
            class="flex items-center justify-center px-2 h-6 rounded-lg cursor-pointer font-bold transition-all"
            :class="speed === 'paused' ? 'bg-rose-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
            style="font-size: 11px;"
            title="Pause Simulation"
        >
            <x-lucide-pause class="w-3 h-3" />
        </button>
        <button
            @click="setSpeed('1x')"
            class="flex items-center justify-center px-2 h-6 rounded-lg cursor-pointer font-bold transition-all"
            :class="speed === '1x' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
            style="font-size: 11px;"
        >
            1&times;
        </button>
        <button
            @click="setSpeed('2x')"
            class="flex items-center justify-center px-2 h-6 rounded-lg cursor-pointer font-bold transition-all"
            :class="speed === '2x' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
            style="font-size: 11px;"
        >
            2&times;
        </button>
        <button
            @click="setSpeed('4x')"
            class="flex items-center justify-center px-2 h-6 rounded-lg cursor-pointer font-bold transition-all"
            :class="speed === '4x' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900'"
            style="font-size: 11px;"
        >
            4&times;
        </button>
    </div>

    <!-- Right Profile / Alerts -->
    <div class="flex items-center gap-1.5 flex-shrink-0">
        <a href="{{ route('notifications.index') }}" class="relative flex items-center justify-center w-8 h-8 rounded-xl cursor-pointer transition-all bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 shadow-xs">
            <x-lucide-bell class="w-4 h-4" />
            @if($unreadAlertsCount > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-white font-bold bg-rose-500 text-[10px] shadow-sm animate-bounce">
                    {{ $unreadAlertsCount }}
                </span>
            @endif
        </a>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 pl-1 pr-2.5 h-8 rounded-xl cursor-pointer transition-all bg-slate-100 hover:bg-slate-200 text-slate-700">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white font-extrabold bg-gradient-to-br from-blue-500 to-indigo-600 shadow-xs text-[11px]">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AV', 0, 2)) }}
                    </div>
                    <span class="text-xs font-bold hidden sm:inline">{{ Auth::user()->name ?? 'CEO' }}</span>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400" />
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="px-4 py-2.5 text-xs text-slate-500 border-b border-slate-100 bg-slate-50 rounded-t-lg">
                    <div class="font-bold text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="truncate text-[11px]">{{ Auth::user()->email }}</div>
                </div>

                <x-dropdown-link :href="route('profile.edit')" class="font-semibold text-slate-700 hover:bg-slate-50">
                    {{ __('Executive Settings') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" class="font-semibold text-rose-600 hover:bg-rose-50"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out Game') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</div>
