<?php

namespace Database\Seeders;

use App\Enums\AttachmentPath;
use App\Models\Agenda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agenda = Agenda::first();

        $path = sprintf('%s/%s', AttachmentPath::AGENDA_BANNERS->value, $agenda->id);
        $bannerPath = Storage::disk('public')->putFile($path, __DIR__ . '/banner-assets/1.jpeg');

        $agenda->banners()->create([
            'image_path' => $bannerPath,
            'image_disk' => 'public',
            'description' => 'Banner 1',
        ]);
    }
}
