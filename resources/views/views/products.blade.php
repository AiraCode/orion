<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ createModal: false }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Product Engineering & Portfolio</h1>
                    <span class="badge badge-blue">Hardware Lines</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Active Production Blueprints & Bill of Materials</p>
            </div>
            <button
                @click="createModal = true"
                class="btn-game-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer"
            >
                <x-lucide-plus class="w-4 h-4" />
                <span>New Product Architecture</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Product Cards -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($products as $product)
                    <div class="game-card p-5 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <span class="text-[10px] font-mono font-extrabold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md uppercase tracking-wider">{{ $product->sku }}</span>
                                    <h3 class="text-base font-extrabold text-slate-900 mt-1 font-heading group-hover:text-blue-600 transition-colors">{{ $product->name }}</h3>
                                    <span class="text-xs font-semibold text-slate-500">{{ $product->category }}</span>
                                </div>
                                <span class="badge badge-green shadow-xs">
                                    {{ $product->status }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 mb-4 line-clamp-2">{{ $product->description }}</p>

                            <!-- Financial KPIs for product -->
                            <div class="grid grid-cols-2 gap-3 mb-4 p-3 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Selling Price</div>
                                    <div class="text-sm font-extrabold font-mono text-emerald-600">${{ number_format($product->selling_price, 2) }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Target Unit Cost</div>
                                    <div class="text-sm font-extrabold font-mono text-rose-500">${{ number_format($product->target_unit_cost, 2) }}</div>
                                </div>
                            </div>

                            <!-- Quality & Appeal -->
                            <div class="space-y-3 mb-4">
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-slate-600 font-bold">Quality Rating</span>
                                        <span class="font-mono text-slate-900 font-extrabold">{{ $product->quality_rating }}%</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100 border border-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-sky-400" style="width: {{ $product->quality_rating }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-slate-600 font-bold">Market Appeal</span>
                                        <span class="font-mono text-slate-900 font-extrabold">{{ $product->appeal_score }}%</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100 border border-slate-200 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500" style="width: {{ $product->appeal_score }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bill of Materials (BOM) -->
                            <div class="border-t border-slate-100 pt-3">
                                <div class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 mb-2">Bill of Materials (BOM Components)</div>
                                <div class="space-y-1.5">
                                    @foreach($product->rawMaterials as $mat)
                                        <div class="flex items-center justify-between text-xs text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                                            <span class="font-medium flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                {{ $mat->name }}
                                            </span>
                                            <span class="font-mono text-blue-600 font-bold bg-blue-100/60 px-1.5 py-0.5 rounded text-[11px]">{{ $mat->pivot->quantity_required }}x</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Create Product Modal -->
        <div x-show="createModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs" style="display: none;">
            <div class="w-full max-w-md p-6 rounded-2xl border border-slate-200 bg-white shadow-2xl animate-pop" @click.outside="createModal = false">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 font-heading">Design New Product Architecture</h3>
                    <button @click="createModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                
                <form action="{{ route('products.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Product Name</label>
                        <input type="text" name="name" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="e.g. ApexDrone Autonomous Scout">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">SKU</label>
                            <input type="text" name="sku" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="PROD-DRONE-03">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category</label>
                            <input type="text" name="category" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" value="Commercial Drones">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Selling Price ($)</label>
                            <input type="number" step="0.01" name="selling_price" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" value="4500.00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Unit Cost ($)</label>
                            <input type="number" step="0.01" name="target_unit_cost" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" value="1800.00">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Architecture Description</label>
                        <textarea name="description" rows="2" class="w-full p-2.5 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Specifications and key competitive advantages..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="createModal = false" class="px-4 h-9 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all">Cancel</button>
                        <button type="submit" class="btn-game-primary px-5 h-9 text-xs cursor-pointer">Create Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
