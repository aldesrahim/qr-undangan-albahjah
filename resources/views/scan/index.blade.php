<x-app-layout class="bg-gradient-to-tl from-amber-100 via-transparent to-rose-100 min-h-screen">
    <x-slot name="title">
        Merajut Cinta Menuju Bulan Mulia 1445 H
    </x-slot>

    <x-slot name="heading">
        {!! $metaData !!}
    </x-slot>

    <div class="relative overflow-hidden">
        <x-decoration />

        <main class="my-16 mx-auto max-w-7xl px-4 sm:mt-24 relative">
            <div class="text-center">
                <h1 class="text-4xl tracking-tight drop-shadow-md font-extrabold text-amber-500 sm:text-5xl md:text-6xl">
                    <span class="block xl:inline">{{ $agenda->name }}</span>
                </h1>
                <div class="mt-3 max-w-xl mx-auto text-base text-gray-600 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    <p>{{ $agenda->started_at->toReadableDateTime() }}</p>
                    <p class="text-base">s/d</p>
                    <p>{{ $agenda->isUncertain() ? 'Selesai' : $agenda->finished_at->toReadableDateTime() }}</p>
                </div>

                <x-divider class="my-5 lg:my-8">Deskripsi Acara</x-divider>

                <p class="text-base mx-auto max-w-2xl text-gray-700">
                    {{ $agenda->description }}
                </p>

                @if(filled($agenda->invitation))
                    @include('scan.invitation')
                @endif

                @if(filled($agenda->banners))
                    @include('scan.banners')
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
