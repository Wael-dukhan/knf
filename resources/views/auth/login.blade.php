<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <style>
        html[lang='ar'] form {
            text-align: end;
        }
    </style>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        {{-- <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div> --}}
       <!-- Password -->
       <div class="mt-4 relative">
        <x-input-label for="password" :value="__('Password')" />

        <div class="relative">
            <x-text-input id="password" class="block mt-1 w-full pr-10" 
                        type="password"
                        name="password"
                        required autocomplete="current-password" />

            <!-- Toggle Eye Icon -->
            <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 cursor-pointer" style="margin-top: -29px;cursor: pointer;">
                <!-- عين مغلقة (Font Awesome) -->
                <i id="eyeClosed" class="fa-solid fa-eye-slash text-gray-600 w-6 h-6"></i>

                <!-- عين مفتوحة (Font Awesome) -->
                <i id="eyeOpened" class="fa-solid fa-eye text-gray-600 w-6 h-6 hidden"></i>
            </span>
        </div>

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
        
   
    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        const eyeClosed = document.getElementById("eyeClosed");
        const eyeOpened = document.getElementById("eyeOpened");

        togglePassword.addEventListener("click", function() {
            // تغيير نوع الحقل بين "password" و "text"
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;

            // التبديل بين الأيقونات
            if (type === "password") {
                eyeClosed.classList.remove("hidden");
                eyeOpened.classList.add("hidden");
            } else {
                eyeClosed.classList.add("hidden");
                eyeOpened.classList.remove("hidden");
            }
        });
    </script>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    
</x-guest-layout>
