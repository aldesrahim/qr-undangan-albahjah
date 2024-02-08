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
            ['name' => 'description', 'property' => 'og:description', 'content' => $description],

            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:url', 'content' => $appUrl],
            ['property' => 'og:title', 'content' => $title],

            ['property' => 'twitter:card', 'content' => 'summary_large_image'],
            ['property' => 'twitter:url', 'content' => $appUrl],
            ['property' => 'twitter:title', 'content' => $title],
            ['property' => 'twitter:description', 'content' => $description],
        ];

        //if (filled($bannerUrl)) {
        //    $bannerUrl = 'https://picperf.io/' . $bannerUrl;

        //    $meta[] = ['property' => 'og:image', 'content' => $bannerUrl];
        //    $meta[] = ['property' => 'twitter:image', 'content' => $bannerUrl];
        //}

        if (filled($qrUrl)) {
            $meta[] = ['name' => 'image', 'property' => 'og:image', 'content' => $qrUrl];
            $meta[] = ['property' => 'twitter:image', 'content' => $qrUrl];
        }

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
