<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key', '');
    }

    // Şehre göre hava durumu getir (1 saat cache)
    public function getByCity(string $city): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $cacheKey = 'weather_' . Str::slug($city);

        return Cache::remember($cacheKey, 3600, function () use ($city) {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/weather", [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'lang' => 'tr',
                    'units' => 'metric',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            } catch (\Exception $e) {
                Log::warning('WeatherService hata: ' . $e->getMessage());
                return null;
            }
        });
    }

    // Hava durumu gecikme uyarısı gerektiriyor mu?
    public function isDelayWarning(?array $weatherData): bool
    {
        if (!$weatherData) return false;

        $condition = $weatherData['weather'][0]['main'] ?? '';
        $warningConditions = ['Rain', 'Storm', 'Snow',
            'Thunderstorm', 'Drizzle', 'Blizzard'];

        return in_array($condition, $warningConditions);
    }

    // Hava durumu açıklaması
    public function getDescription(?array $weatherData): string
    {
        if (!$weatherData) return '';
        return $weatherData['weather'][0]['description'] ?? '';
    }

    // Sıcaklık
    public function getTemperature(?array $weatherData): ?float
    {
        if (!$weatherData) return null;
        return $weatherData['main']['temp'] ?? null;
    }

    // İkon kodu
    public function getIconUrl(?array $weatherData): string
    {
        if (!$weatherData) return '';
        $icon = $weatherData['weather'][0]['icon'] ?? '';
        return $icon ? "https://openweathermap.org/img/wn/{$icon}@2x.png" : '';
    }
}
