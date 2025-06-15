<x-guest-layout>
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-tr from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 px-6 py-12">
      <div class="w-full max-w-md rounded-xl bg-white p-8 shadow-lg dark:bg-gray-900">
        <div class="mb-6 text-center">
          <x-authentication-card-logo />
          <h2 class="mt-4 text-2xl font-bold text-gray-800 dark:text-white">Please login to your account</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-8 ">The application is used in the environment of the TNI AU Dirgantara Mandala Museum. Its main focus is on recording employee attendance in a practical and efficient manner.</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
          <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ $value }}
          </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
          @csrf

          <!-- Email or Phone -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ __('Email or Phone') }}
            </label>
            <div class="relative mt-1">
              <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 pl-10 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400" />
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M16 12A4 4 0 1 1 8 12a4 4 0 0 1 8 0z" />
                  <path d="M2 20h20" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              {{ __('Password') }}
            </label>
            <div class="relative mt-1">
              <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full rounded-md border border-gray-300 bg-white px-4 py-2 pl-10 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400" />
              <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M12 11c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
                  <path d="M4 8v10c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-8-5-8 5z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <x-checkbox name="remember" id="remember_me" checked />
              <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition duration-150">
                {{ __('Forgot password?') }}
              </a>
            @endif
          </div>

          <!-- Buttons -->
          <div class="flex items-center justify-between">
            <a href="{{ route('register') }}">
              <x-secondary-button>
                {{ __('Register') }}
              </x-secondary-button>
            </a>

            <x-button>
              {{ __('Log in') }}
            </x-button>
          </div>
        </form>
      </div>
    </div>
  </x-guest-layout>
