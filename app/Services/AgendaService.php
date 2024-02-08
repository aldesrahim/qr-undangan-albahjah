<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Builder;

class AgendaService
{
    public function findById(string $id, ?string $invitationCode = null)
    {
        return Agenda::query()
            ->whereKey($id)
            ->with('banners')
            ->when(
                $invitationCode,
                fn (Builder $query) => $query
                ->with([
                    'invitation' => fn ($query) => $query->where('code', $invitationCode),
                    'invitation.visitor.categories',
                ])
            )
            ->first();
    }

    public function getMetaData(Agenda $agenda, bool $asHtml = true): array|string
    {
        $appUrl = config('app.url');
        $title = $agenda->name;
        $description = $agenda->short_description ?? $title;

        $qrUrl = $agenda->invitation?->qr_url;
        $banner = $agenda->banners?->first();
        $bannerUrl = $banner?->image_url;

        $meta = [
            ['name' => 'title', 'content' => $title],
            ['name' => 'description', 'content' => $description],

            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:title', 'content' => $title],
            ['property' => 'og:description', 'content' => $description],

            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:title', 'content' => $title],
            ['property' => 'og:image', 'content' => $qrUrl],
            ['property' => 'og:description', 'content' => $description],
            ['property' => 'og:url', 'content' => $appUrl],
            ['property' => 'og:site_name', 'content' => config('app.name')],
            ['property' => 'og:locale', 'content' => 'id_ID'],

            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['name' => 'twitter:url', 'content' => $appUrl],
            ['name' => 'twitter:title', 'content' => $title],
            ['name' => 'twitter:description', 'content' => $description],
            ['property' => 'twitter:image', 'content' => $qrUrl]
        ];

        if (!$asHtml) {
            return $meta;
        }

        $tags = '';

        foreach ($meta as $item) {
            $tag = '<meta ';
            foreach ($item as $key => $value) {
                $tag .= "$key=\"$value\" ";
            }
            $tag .= '/>';

            $tags .= ($tag . PHP_EOL);
        }

        return $tags;
    }
}
