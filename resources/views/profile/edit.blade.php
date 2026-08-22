<x-app-layout>
    <div class="h-full flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-white/70 backdrop-blur-md border-b border-slate-200 flex-shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-extrabold text-slate-900 font-heading">{{ __('Executive Account & Settings') }}</h1>
                    <span class="badge badge-gray">System Preferences</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">{{ __('Manage your CEO account, credentials and authentication settings') }}</p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div class="max-w-4xl space-y-6">
                <div class="p-6 game-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-6 game-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-6 game-card border-rose-200 bg-rose-50/20">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
