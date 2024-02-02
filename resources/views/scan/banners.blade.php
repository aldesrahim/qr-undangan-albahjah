<x-divider class="my-5 lg:my-8">Galeri</x-divider>

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
