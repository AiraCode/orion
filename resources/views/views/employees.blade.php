<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden" x-data="{ hireModal: false, selectedDept: {{ $departments->first()?->id ?? 'null' }} }">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">Human Capital & Executive Staff</h1>
                    <span class="badge badge-green">Talent Engine</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ $company->name }} &middot; Talent Acquisition, Morale & Corporate Payroll</p>
            </div>
            <button
                @click="hireModal = true"
                class="btn-game-success px-4 py-2 text-xs flex items-center gap-2 cursor-pointer"
            >
                <x-lucide-user-plus class="w-4 h-4" />
                <span>Recruit Talent</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs flex items-center gap-2 shadow-xs">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- HR KPIs -->
        <div class="p-6 pb-0 flex-shrink-0">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Total Headcount</span>
                        <div class="w-6 h-6 rounded-lg bg-blue-500 text-white flex items-center justify-center">
                            <x-lucide-users class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-slate-900 mt-1">{{ $employees->count() }} Staff</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Monthly Payroll</span>
                        <div class="w-6 h-6 rounded-lg bg-rose-500 text-white flex items-center justify-center">
                            <x-lucide-dollar-sign class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-rose-500 mt-1">${{ number_format($totalSalaries, 2) }}</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Average Morale</span>
                        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center">
                            <x-lucide-smile class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-emerald-600 mt-1">{{ number_format($avgMorale, 1) }}%</div>
                </div>
                <div class="kpi-card">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-bold uppercase tracking-wider">Productivity</span>
                        <div class="w-6 h-6 rounded-lg bg-indigo-500 text-white flex items-center justify-center">
                            <x-lucide-zap class="w-3.5 h-3.5" />
                        </div>
                    </div>
                    <div class="text-2xl font-extrabold font-mono text-indigo-600 mt-1">{{ number_format($avgProductivity, 1) }}%</div>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Departments Overview -->
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-3">Corporate Divisions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($departments as $dept)
                        <div class="game-card p-4">
                            <div class="text-[10px] font-mono text-blue-600 font-extrabold uppercase bg-blue-50 px-2 py-0.5 rounded-md inline-block">{{ $dept->code }}</div>
                            <div class="text-sm font-extrabold text-slate-900 mt-1 mb-2 font-heading">{{ $dept->name }}</div>
                            <div class="flex justify-between items-center text-xs text-slate-500 font-mono pt-2 border-t border-slate-100">
                                <span class="font-medium">Active Team:</span>
                                <span class="text-slate-900 font-extrabold bg-slate-100 px-2 py-0.5 rounded-md">{{ $dept->employees_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Employee Roster -->
            <div class="game-card p-5">
                <h3 class="text-sm font-extrabold text-slate-900 font-heading mb-4">Employee Roster & Performance Ratings</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Role Title</th>
                                <th>Department</th>
                                <th class="text-right">Monthly Salary</th>
                                <th class="text-right">Skill Rating</th>
                                <th class="text-right">Morale</th>
                                <th class="text-right">Productivity</th>
                            </tr>
                        </thead>
                        <tbody class="font-mono">
                            @foreach($employees as $emp)
                                <tr>
                                    <td class="font-sans font-extrabold text-slate-900">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                                {{ substr($emp->first_name, 0, 1) }}{{ substr($emp->last_name, 0, 1) }}
                                            </div>
                                            <span>{{ $emp->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="font-sans font-semibold text-slate-700">{{ $emp->role_title }}</td>
                                    <td class="font-sans text-slate-600">{{ $emp->department?->name }}</td>
                                    <td class="text-right font-extrabold text-slate-900">${{ number_format($emp->salary, 2) }}</td>
                                    <td class="text-right font-bold text-sky-600">{{ $emp->skill_level }}%</td>
                                    <td class="text-right font-bold text-emerald-600">{{ $emp->morale }}%</td>
                                    <td class="text-right font-bold text-blue-600">{{ $emp->productivity }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hire Modal -->
        <div x-show="hireModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs" style="display: none;">
            <div class="w-full max-w-md p-6 rounded-2xl border border-slate-200 bg-white shadow-2xl animate-pop" @click.outside="hireModal = false">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-900 font-heading">Recruit Key Talent</h3>
                    <button @click="hireModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <form action="{{ route('employees.hire') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">First Name</label>
                            <input type="text" name="first_name" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="e.g. Victor">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Last Name</label>
                            <input type="text" name="last_name" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="e.g. Zhao">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Role Title</label>
                        <input type="text" name="role_title" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" placeholder="e.g. Lead Sensor Calibration Specialist">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Department</label>
                        <select name="department_id" x-model="selectedDept" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Monthly Salary ($)</label>
                        <input type="number" min="1000" step="100" name="salary" required class="w-full h-9 px-3 rounded-xl text-xs text-slate-800 bg-slate-50 border border-slate-200 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 outline-none" value="12500">
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="hireModal = false" class="px-4 h-9 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 cursor-pointer transition-all">Cancel</button>
                        <button type="submit" class="btn-game-success px-5 h-9 text-xs cursor-pointer">Hire Candidate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
