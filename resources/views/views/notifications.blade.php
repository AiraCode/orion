<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Intelligence & Operational Intel</h1>
                    <span class="badge badge-amber">Live Telemetry</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Real-time Operational Events & Executive Alerts</p>
            </div>
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-xs transition-all cursor-pointer">
                    Mark All as Read
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Alerts List -->
        <div class="flex-1 overflow-y-auto p-6 space-y-3.5">
            @forelse($alerts as $alert)
                @php
                    $colors = [
                        'critical' => 'border-rose-200 bg-rose-50/70 text-rose-900',
                        'warning' => 'border-amber-200 bg-amber-50/70 text-amber-900',
                        'success' => 'border-emerald-200 bg-emerald-50/70 text-emerald-900',
                        'info' => 'border-blue-200 bg-blue-50/70 text-blue-900',
                    ];
                    $badgeClass = $colors[$alert->type] ?? $colors['info'];
                @endphp
                <div class="p-4 rounded-2xl border {{ $badgeClass }} flex justify-between items-start transition-all shadow-xs hover:shadow-md">
                    <div class="flex gap-3.5 items-start">
                        <div class="mt-0.5 w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 {{ $alert->type === 'critical' ? 'bg-rose-100 text-rose-600' : ($alert->type === 'warning' ? 'bg-amber-100 text-amber-600' : ($alert->type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600')) }}">
                            @if($alert->type === 'critical')
                                <x-lucide-alert-triangle class="w-4 h-4" />
                            @elseif($alert->type === 'warning')
                                <x-lucide-alert-circle class="w-4 h-4" />
                            @elseif($alert->type === 'success')
                                <x-lucide-check-circle-2 class="w-4 h-4" />
                            @else
                                <x-lucide-info class="w-4 h-4" />
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-extrabold text-slate-900 font-heading">{{ $alert->title }}</h4>
                                <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-mono font-bold bg-white/80 border border-slate-200 text-slate-600">
                                    {{ $alert->category }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $alert->message }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400 flex-shrink-0 bg-white/60 px-2 py-1 rounded-lg border border-slate-100">{{ $alert->game_date->format('M d, Y') }}</span>
                </div>
            @empty
                <div class="text-center py-16 text-slate-400 text-xs font-sans">
                    No notifications or active alerts. Systems are running smoothly!
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
