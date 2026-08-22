<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ rdModal: false }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Research & Deep Tech Innovation</h1>
                    <span class="badge badge-purple">Proprietary IP</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Hardware Architecture & Breakthrough Tech Pipelines</p>
            </div>
            <button
                @click="rdModal = true"
                class="btn-game-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-violet-500/20"
            >
                <x-lucide-flask-conical class="w-4 h-4" />
                <span>Initiate R&D Project</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Active R&D Projects -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($projects as $project)
                    <div class="game-card p-5 flex flex-col justify-between group">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-[10px] font-mono text-violet-600 font-extrabold uppercase tracking-wider bg-violet-50 px-2 py-0.5 rounded border border-violet-200">{{ $project->category }}</span>
                                    <h3 class="text-base font-extrabold text-slate-900 font-heading mt-1.5">{{ $project->name }}</h3>
                                </div>
                                <span class="badge {{ $project->status === 'completed' ? 'badge-green' : 'badge-purple' }}">
                                    {{ str_replace('_', ' ', $project->status) }}
                                </span>
                            </div>

                            <!-- Progress Meter -->
                            <div class="my-4 bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                                <div class="flex justify-between text-xs font-mono mb-1.5">
                                    <span class="text-slate-600 font-bold">Research Milestones:</span>
                                    <span class="text-violet-700 font-extrabold">{{ number_format($project->progress_pct, 1) }}%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 via-indigo-500 to-sky-400 animate-stripes" style="width: {{ $project->progress_pct }}%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-mono">
                                <div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase">Daily Budget</div>
                                    <div class="font-extrabold text-slate-900">${{ number_format($project->daily_budget, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase">Target Budget</div>
                                    <div class="font-extrabold text-sky-600">${{ number_format($project->total_budget, 0) }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase">Spent to Date</div>
                                    <div class="font-extrabold text-rose-500">${{ number_format($project->spent_amount, 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-12 text-slate-400 text-xs font-sans">
                        No active R&D projects. Click "Initiate R&D Project" to begin breakthrough research.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Initiate Project Modal -->
        <div x-show="rdModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs" style="display: none;">
            <div class="w-full max-w-md p-6 rounded-2xl border border-slate-200 bg-white shadow-2xl animate-pop" @click.outside="rdModal = false">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 font-heading">Launch Breakthrough Research</h3>
                    <button @click="rdModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <form action="{{ route('rd.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Project Name</label>
                        <input type="text" name="name" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none" placeholder="e.g. Next-Gen Solid-State Battery Pack">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tech Category</label>
                        <input type="text" name="category" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none" value="Energy Storage">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Daily Budget ($)</label>
                            <input type="number" min="500" name="daily_budget" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none" value="5000">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Budget ($)</label>
                            <input type="number" min="10000" name="total_budget" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-100 outline-none" value="200000">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="rdModal = false" class="px-4 h-9 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all">Cancel</button>
                        <button type="submit" class="btn-game-primary px-5 h-9 text-xs cursor-pointer bg-gradient-to-r from-violet-600 to-indigo-600">Initiate Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
