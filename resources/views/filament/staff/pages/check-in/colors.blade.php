<div class="grid gap-2">
    <x-filament-forms::field-wrapper.label>
        Kategori Warna
    </x-filament-forms::field-wrapper.label>

    <div class="flex gap-2">
        @foreach($getState() ?? [] as $category)
            <span class="flex items-center gap-x-1 border rounded-md p-1 text-sm">
                <div class="h-5 w-5 rounded" style="background-color: {{ $category->color }}">
                    &nbsp;
                </div>
                {{ $category->name }}
            </span>
        @endforeach
    </div>
</div>
