<x-app-layout>
    <x-slot name="title">
        Merajut Cinta Menuju Bulan Mulia 1445 H
    </x-slot>

    <x-slot name="heading">
        {!! $metaData !!}
    </x-slot>

    <main class="my-16 mx-auto max-w-7xl px-4 sm:mt-24">
        <div class="text-center">
            <h1 class="text-4xl tracking-tight font-extrabold text-amber-500 sm:text-5xl md:text-6xl">
                <span class="block xl:inline">{{ $agenda->name }}</span>
            </h1>
            <div class="mt-3 max-w-xl mx-auto text-base text-gray-600 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                <p>{{ $agenda->started_at->toReadableDateTime() }}</p>
                <p class="text-base">s/d</p>
                <p>{{ $agenda->isUncertain() ? 'Selesai' : $agenda->finished_at->toReadableDateTime() }}</p>
            </div>
            <div class="relative my-5 lg:my-8">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-2 bg-white text-sm lg:text-base text-amber-500"> Deskripsi Acara </span>
                </div>
            </div>
            <p class="text-base mx-auto max-w-2xl text-gray-700">
                {{ $agenda->description }}
            </p>
            @if(filled($agenda->invitation))
                <div class="relative my-5 lg:my-8">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-2 bg-white text-sm lg:text-base text-amber-500"> Undangan Untuk Anda </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-6">
                    <div class="bg-white shadow overflow-hidden rounded-md">
                        <ul role="list" class="divide-y divide-gray-200">
                            <li class="px-6 py-4">
                                <div class="text-left">
                                    <p class="text-amber-600 text-base">Nama</p>
                                    <p class="text-base md:text-xl">
                                        {{ $agenda->invitation->visitor->name }}
                                    </p>
                                </div>
                            </li>

                            @if(!empty($agenda->invitation->companion))
                                <li class="px-6 py-4">
                                    <div class="text-left">
                                        <p class="text-amber-600 text-base">Jumlah Pendamping</p>
                                        <p class="text-base md:text-xl">
                                            {{ $agenda->invitation->companion }}
                                        </p>
                                    </div>
                                </li>
                            @endif

                            <li class="px-6 py-4">
                                <div class="text-left">
                                    <p class="text-amber-600 text-base">Nomor Telepon</p>
                                    <p class="text-base md:text-xl">
                                        {{ $agenda->invitation->visitor->phone_number }}
                                    </p>
                                </div>
                            </li>
                            <li class="px-6 py-4">
                                <div class="text-left">
                                    <p class="text-amber-600 text-base">Alamat</p>
                                    <p class="text-base md:text-xl">
                                        {{ $agenda->invitation->visitor->address }}
                                    </p>
                                </div>
                            </li>

                            @if(filled($agenda->invitation->visitor->categories))
                                <li class="px-6 py-4">
                                    <div class="text-left">
                                        <p class="text-amber-600 text-base">Kategori</p>
                                        <p class="text-base md:text-xl">
                                            {!! $agenda->invitation->visitor->categories->pluck('label')->join('<br />') !!}
                                        </p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="overflow-hidden rounded-md">
                        <div class="px-6 py-4">
                            <p class="text-base lg:text-xl my-5">Tunjukan QR Code ini pada petugas</p>
                            <div class="group block">
                                <img
                                    class="mx-auto object-cover h-auto w-1/3 lg:w-1/2"
                                    src="{{ $agenda->invitation->qr_url }}"
                                    alt="{{ $agenda->invitation->code }}"
                                >
                            </div>
                            <p class="bg-amber-200 border border-amber-400 mt-5 p-5 rounded-md">
                                {{ $agenda->invitation->code }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(filled($agenda->banners))
            <div class="relative my-5 lg:my-8">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-2 bg-white text-sm lg:text-base text-amber-500"> Galeri </span>
                </div>
            </div>

            <ul role="list" class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2 sm:gap-x-6 lg:grid-cols-4 xl:gap-x-3">
                @foreach($agenda->banners as $banner)
                    <li class="relative">
                        <a
                            href="{{ $banner->image_url }}"
                            target="_blank"
                            class="group block w-full aspect-w-10 aspect-h-7 rounded-lg bg-gray-100 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-offset-gray-100 focus-within:ring-indigo-500 overflow-hidden">
                            <img
                                src="{{ $banner->image_url }}"
                                alt="{{ $banner->description }}"
                                class="object-cover pointer-events-none group-hover:opacity-75"
                            >
                        </a>
                        <p class="mt-2 block text-sm font-medium text-gray-900 truncate pointer-events-none">
                            {{ $banner->description }}
                        </p>
                    </li>
                @endforeach
            </ul>
            @endif
        </div>
    </main>
</x-app-layout>
