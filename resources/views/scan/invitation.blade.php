<x-divider class="my-5 lg:my-8">Undangan Untuk Anda</x-divider>

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
        </ul>
    </div>
    <div class="overflow-hidden rounded-md">
        <div class="px-6 py-4">
            <p class="text-base lg:text-xl my-5">Tunjukan QR Code ini pada petugas</p>
            <div class="group block">
                <img
                    class="mx-auto object-cover h-auto w-full sm:w-1/2"
                    src="{{ $agenda->invitation->qr_url }}"
                    alt="{{ $agenda->invitation->code }}"
                >
            </div>
        </div>
    </div>
</div>
