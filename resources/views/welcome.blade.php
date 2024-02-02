<x-app-layout class="bg-gradient-to-tl from-amber-100 via-transparent to-rose-100 min-h-screen">
    <div class="relative overflow-hidden">
        <x-decoration />

        <main class="my-16 mx-auto max-w-7xl px-4 sm:mt-24 relative">
            <div class="text-center">
                <h1 class="text-4xl tracking-tight drop-shadow-md font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block xl:inline">{{ config('app.name') }}</span>
                </h1>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="{{ route('filament.admin.auth.login') }}" class="w-full flex items-center justify-center px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-amber-500 hover:bg-amber-600 md:text-lg">
                            Admin
                        </a>
                    </div>
                    <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                        <a href="{{ route('filament.staff.auth.login') }}" class="w-full flex items-center justify-center px-6 py-2 border border-transparent text-base font-medium rounded-md text-amber-600 border border-amber-700 bg-white hover:bg-gray-50 md:text-lg">
                            Staff
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
