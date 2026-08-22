@props(['active' => null])

@php
$navItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'lucide-layout-dashboard', 'color' => 'text-blue-500', 'activeColor' => 'text-blue-600 bg-blue-100', 'section' => null, 'route' => 'dashboard'],
    ['id' => 'products', 'label' => 'Products', 'icon' => 'lucide-package', 'color' => 'text-sky-500', 'activeColor' => 'text-sky-600 bg-sky-100', 'section' => 'OPERATIONS', 'route' => 'products.index'],
    ['id' => 'procurement', 'label' => 'Procurement', 'icon' => 'lucide-shopping-cart', 'color' => 'text-amber-500', 'activeColor' => 'text-amber-600 bg-amber-100', 'section' => null, 'route' => 'procurement.index'],
    ['id' => 'production', 'label' => 'Production', 'icon' => 'lucide-factory', 'color' => 'text-orange-500', 'activeColor' => 'text-orange-600 bg-orange-100', 'section' => null, 'route' => 'production.index'],
    ['id' => 'inventory', 'label' => 'Inventory', 'icon' => 'lucide-archive', 'color' => 'text-indigo-500', 'activeColor' => 'text-indigo-600 bg-indigo-100', 'section' => null, 'route' => 'inventory.index'],
    ['id' => 'market', 'label' => 'Market & Sales', 'icon' => 'lucide-globe', 'color' => 'text-rose-500', 'activeColor' => 'text-rose-600 bg-rose-100', 'section' => 'COMMERCIAL', 'route' => 'market.index'],
    ['id' => 'employees', 'label' => 'Employees & HR', 'icon' => 'lucide-users', 'color' => 'text-emerald-500', 'activeColor' => 'text-emerald-600 bg-emerald-100', 'section' => 'PEOPLE & FINANCE', 'route' => 'employees.index'],
    ['id' => 'finance', 'label' => 'Financial Core', 'icon' => 'lucide-dollar-sign', 'color' => 'text-green-500', 'activeColor' => 'text-green-600 bg-green-100', 'section' => null, 'route' => 'finance.index'],
    ['id' => 'investors', 'label' => 'Cap Table & Investors', 'icon' => 'lucide-trending-up', 'color' => 'text-purple-500', 'activeColor' => 'text-purple-600 bg-purple-100', 'section' => null, 'route' => 'investors.index'],
    ['id' => 'rd', 'label' => 'R&D Innovation', 'icon' => 'lucide-flask-conical', 'color' => 'text-violet-500', 'activeColor' => 'text-violet-600 bg-violet-100', 'section' => 'INTELLIGENCE', 'route' => 'rd.index'],
    ['id' => 'notifications', 'label' => 'Alerts & Intel', 'icon' => 'lucide-bell', 'color' => 'text-amber-500', 'activeColor' => 'text-amber-600 bg-amber-100', 'section' => null, 'route' => 'notifications.index'],
    ['id' => 'settings', 'label' => 'Settings', 'icon' => 'lucide-settings', 'color' => 'text-slate-500', 'activeColor' => 'text-slate-600 bg-slate-200', 'section' => 'SYSTEM', 'route' => 'profile.edit'],
];
@endphp

<div
    class="flex flex-col h-full overflow-y-auto overflow-x-hidden transition-all duration-200 flex-shrink-0 bg-white/90 backdrop-blur-md border-r border-slate-200 shadow-xs z-20"
    :style="sidebarCollapsed ? 'width: 60px;' : 'width: 210px;'"
>
    <!-- Nav Items -->
    <div class="flex-1 pt-3 pb-4 px-2.5 overflow-y-auto space-y-0.5">
        @foreach($navItems as $item)
            @php
                $isActive = $active
                    ? ($active === $item['id'])
                    : (
                        request()->routeIs($item['route']) ||
                        request()->routeIs(explode('.', $item['route'])[0] . '.*') ||
                        request()->is($item['id']) ||
                        request()->is($item['id'] . '/*') ||
                        ($item['id'] === 'settings' && (request()->routeIs('profile.*') || request()->is('profile*')))
                    );
            @endphp
            <div>
                @if($item['section'])
                    <div x-show="!sidebarCollapsed" class="px-2.5 pt-4 pb-1 text-[10px] font-extrabold text-slate-400 tracking-wider uppercase font-heading">
                        {{ $item['section'] }}
                    </div>
                    <div x-show="sidebarCollapsed" class="my-2 mx-1 border-t border-slate-200"></div>
                @endif
                <a
                    href="{{ route($item['route']) }}"
                    class="sidebar-item w-full text-left flex items-center group {{ $isActive ? 'active' : 'border-transparent' }}"
                    :style="sidebarCollapsed ? 'justify-content: center; padding-left: 0; padding-right: 0;' : 'justify-content: flex-start'"
                    :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''"
                >
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-all flex-shrink-0 {{ $isActive ? $item['activeColor'] : 'group-hover:bg-slate-100 ' . $item['color'] }}">
                        <x-dynamic-component :component="$item['icon']" class="w-4 h-4" />
                    </div>
                    <span x-show="!sidebarCollapsed" class="ml-2 font-bold tracking-tight text-[12.5px] truncate">{{ $item['label'] }}</span>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Collapse toggle -->
    <div class="flex-shrink-0 p-2.5 border-t border-slate-200 bg-slate-50/50">
        <button
            @click="sidebarCollapsed = !sidebarCollapsed"
            class="flex items-center justify-center w-full h-8 rounded-xl transition-all cursor-pointer text-slate-500 hover:text-slate-800 hover:bg-slate-200/70"
            title="Toggle Sidebar"
        >
            <x-lucide-chevron-right x-show="sidebarCollapsed" class="w-4 h-4" />
            <x-lucide-chevron-left x-show="!sidebarCollapsed" class="w-4 h-4" />
        </button>
    </div>
</div>
