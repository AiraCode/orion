<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-slate-700">{{ __('CEO Executive Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', 'ceo@novatech.corp') }}" required autofocus autocomplete="username"
                class="w-full h-10 px-3.5 rounded-xl text-sm font-medium outline-none transition-all bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-800" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider mb-1.5 text-slate-700">{{ __('Access Code / Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" value="password"
                class="w-full h-10 px-3.5 rounded-xl text-sm font-medium outline-none transition-all bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-slate-800" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="btn-game-primary w-full h-10 text-sm font-bold cursor-pointer">
                {{ __('Start Simulation Session &rarr;') }}
            </button>
        </div>

        <div class="text-center text-xs mt-4 text-slate-400 font-medium">
            Demo credentials pre-filled &middot; Click to play
        </div>
    </form>
</x-guest-layout>
