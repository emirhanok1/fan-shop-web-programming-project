<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TMDBService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.themoviedb.org/3';
    private string $imageBase = 'https://image.tmdb.org/t/p/';

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key', '');
    }

    // ID ve tip ile detay getir (24 saat cache)
    public function getById(int $tmdbId, string $type = 'tv'): ?array
    {
        if (empty($this->apiKey)) return null;

        $cacheKey = "tmdb_{$type}_{$tmdbId}";

        return Cache::remember($cacheKey, 86400, function () use ($tmdbId, $type) {
            try {
                $endpoint = $type === 'tv'
                    ? "/tv/{$tmdbId}"
                    : "/movie/{$tmdbId}";

                $response = Http::timeout(5)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get($this->baseUrl . $endpoint, [
                        'language' => 'tr-TR',
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }
                return null;
            } catch (\Exception $e) {
                Log::warning('TMDBService getById hata: ' . $e->getMessage());
                return null;
            }
        });
    }

    // Arama (admin quick search için)
    public function search(string $query): array
    {
        if (empty($this->apiKey)) return [];

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/search/multi', [
                    'query' => $query,
                    'language' => 'tr-TR',
                    'page' => 1,
                ]);

            if ($response->successful()) {
                $results = $response->json()['results'] ?? [];
                return array_slice(
                    array_filter($results, fn($r) =>
                        in_array($r['media_type'], ['movie', 'tv'])
                    ),
                    0, 5
                );
            }
            return [];
        } catch (\Exception $e) {
            Log::warning('TMDBService search hata: ' . $e->getMessage());
            return [];
        }
    }

    // Poster URL oluştur
    public function getPosterUrl(?string $path, string $size = 'w500'): ?string
    {
        if (!$path) return null;
        return $this->imageBase . $size . $path;
    }

    // Backdrop URL oluştur
    public function getBackdropUrl(?string $path, string $size = 'w1280'): ?string
    {
        if (!$path) return null;
        return $this->imageBase . $size . $path;
    }

    // TV veya Film için başlık döndür
    public function getTitle(array $data, string $type): string
    {
        return $type === 'tv'
            ? ($data['name'] ?? $data['title'] ?? '')
            : ($data['title'] ?? $data['name'] ?? '');
    }

    // Puan yıldızlarına çevir (10 üzerinden → 5 yıldıza)
    public function getStarRating(?float $voteAverage): float
    {
        return round(($voteAverage ?? 0) / 2, 1);
    }
}
