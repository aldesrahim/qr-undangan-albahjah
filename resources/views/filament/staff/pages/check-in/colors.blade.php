<div class="grid gap-2">
    <x-filament-forms::field-wrapper.label>
        Kategori Warna
    </x-filament-forms::field-wrapper.label>

    <div class="flex gap-2">
        @foreach($getState() ?? [] as $category)
            <div class="h-5 w-5 rounded" style="background-color: {{ $category->color }}">
                &nbsp;
            </div>
        @endforeach
    </div>
</div>
