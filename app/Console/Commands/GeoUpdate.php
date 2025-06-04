<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\City;
use App\Models\Zipcode;

class GeoUpdate extends Command
{
    protected $signature = 'geo:update {--country= : Filter by country code (e.g., UA)}';
    protected $description = 'Sync countries from REST Countries, cities and zipcodes from GeoNames';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        Storage::makeDirectory('geo');
        $start = microtime(true);
        // $this->syncCountriesFromRestCountries();
        $this->syncCities();
        $this->syncZipcodes();
        $this->info('Data updated and synced successfully in ' . (microtime(true) - $start) . ' seconds.');
    }

    protected function syncCountriesFromRestCountries()
    {
        try {
            $response = $this->client->get('https://restcountries.com/v3.1/all', [
                'query' => ['fields' => 'name,cca2,cca3,capital,currencies,idd,region,subregion,timezones,translations,latlng,flag,tld']
            ]);
            $countries = json_decode($response->getBody(), true);

            DB::transaction(function () use ($countries) {
                foreach ($countries as $country) {
                    Country::updateOrCreate(
                        ['iso2' => $country['cca2']],
                        [
                            'name' => $country['name']['common'],
                            'iso3' => $country['cca3'],
                            'numeric_code' => null,
                            'phonecode' => isset($country['idd']['root']) ? ($country['idd']['root'] . ($country['idd']['suffixes'][0] ?? '')) : null,
                            'capital' => $country['capital'][0] ?? null,
                            'currency' => array_key_first($country['currencies']) ?? null,
                            'currency_name' => $country['currencies'][array_key_first($country['currencies'])]['name'] ?? null,
                            'currency_symbol' => $country['currencies'][array_key_first($country['currencies'])]['symbol'] ?? null,
                            'tld' => $country['tld'][0] ?? null,
                            'native' => $country['name']['nativeName'][array_key_first($country['name']['nativeName'])]['common'] ?? $country['name']['common'],
                            'region' => $country['region'] ?? null,
                            'subregion' => $country['subregion'] ?? null,
                            'timezones' => json_encode($country['timezones'] ?? []),
                            'translations' => json_encode($country['translations'] ?? []),
                            'latitude' => $country['latlng'][0] ?? null,
                            'longitude' => $country['latlng'][1] ?? null,
                            'emoji' => $country['flag'] ?? null,
                            'emojiU' => null,
                            'flag' => 1,
                            'wikiDataId' => null,
                        ]
                    );
                }
                \Log::info('Countries synced', ['count' => Country::count()]);
                $this->info('Countries synced from REST Countries.');
            }, 3);
        } catch (\Exception $e) {
            \Log::error('Error syncing countries: ' . $e->getMessage());
            $this->error('Error syncing countries: ' . $e->getMessage());
        }
    }

    protected function syncCities()
    {
        $countryCode = $this->option('country');
        $countries = $countryCode
            ? Country::where('iso2', $countryCode)->pluck('iso2')
            : Country::pluck('iso2');

        $batchSize = 100;
        $start = microtime(true);

        foreach ($countries as $iso2) {
            $zipPath = storage_path("app/geo/{$iso2}.zip");
            $filePath = storage_path("app/geo/{$iso2}.txt");
            $url = "https://download.geonames.org/export/dump/{$iso2}.zip";

            // Завантажуємо ZIP-файл
            $this->info("Downloading $url...");
            try {
                $this->client->get($url, ['sink' => $zipPath]);
                $this->info("Downloaded $zipPath successfully.");
            } catch (\Exception $e) {
                \Log::error("Error downloading $url: " . $e->getMessage());
                $this->error("Error downloading $url: " . $e->getMessage());
                continue;
            }

            // Розпаковуємо ZIP
            try {
                $zip = new \ZipArchive;
                if ($zip->open($zipPath) === true) {
                    $zip->extractTo(storage_path('app/geo'));
                    $zip->close();
                    $this->info("Extracted $zipPath successfully.");
                } else {
                    \Log::error("Failed to extract $zipPath.");
                    $this->error("Failed to extract $zipPath.");
                    Storage::delete("geo/{$iso2}.zip");
                    continue;
                }
            } catch (\Exception $e) {
                \Log::error("Error extracting $zipPath: " . $e->getMessage());
                $this->error("Error extracting $zipPath: " . $e->getMessage());
                Storage::delete("geo/{$iso2}.zip");
                continue;
            }

            // Обробляємо текстовий файл
            if (!file_exists($filePath)) {
                $this->error("$filePath not found after extraction.");
                Storage::delete("geo/{$iso2}.zip");
                continue;
            }

            $file = @fopen($filePath, 'r');
            if ($file === false) {
                $this->error("Failed to open $filePath.");
                Storage::delete(["geo/{$iso2}.zip", "geo/{$iso2}.txt"]);
                continue;
            }

            $citiesBatch = [];
            while (($line = fgets($file)) !== false) {
                $data = explode("\t", trim($line));
                if (count($data) < 19 || $data[7] !== 'PPL' || $data[8] !== $iso2) {
                    continue;
                }

                $citiesBatch[] = [
                    'name' => $data[1],
                    'minimum_free_delivery_order_amount' => 0,
                    'delivery_charges' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($citiesBatch) >= $batchSize) {
                    $this->insertOrUpdateCities($citiesBatch);
                    $citiesBatch = [];
                    gc_collect_cycles();
                    \Log::info("Memory usage after cities batch for $iso2", ['memory' => memory_get_usage() / 1024 / 1024 . ' MB']);
                    usleep(100000);
                }
            }

            if (!empty($citiesBatch)) {
                $this->insertOrUpdateCities($citiesBatch);
            }

            fclose($file);
            Storage::delete(["geo/{$iso2}.zip", "geo/{$iso2}.txt"]);
            $this->info("Cities for $iso2 synced successfully.");
        }

        \Log::info('Cities synced', ['count' => City::count(), 'time' => microtime(true) - $start]);
        $this->info('Cities synced successfully in ' . (microtime(true) - $start) . ' seconds.');
    }

    protected function insertOrUpdateCities(array $citiesBatch)
    {
        DB::transaction(function () use ($citiesBatch) {
            $names = array_column($citiesBatch, 'name');
            $existingCities = City::whereIn('name', $names)->pluck('name')->toArray();

            $newCities = array_filter($citiesBatch, function ($city) use ($existingCities) {
                return !in_array($city['name'], $existingCities);
            });

            if (!empty($newCities)) {
                City::insert($newCities);
            }

            foreach ($citiesBatch as $city) {
                if (in_array($city['name'], $existingCities)) {
                    City::where('name', $city['name'])->update([
                        'minimum_free_delivery_order_amount' => $city['minimum_free_delivery_order_amount'],
                        'delivery_charges' => $city['delivery_charges'],
                        'updated_at' => $city['updated_at'],
                    ]);
                }
            }
        }, 3);
    }

    protected function syncZipcodes()
    {
        $zipPath = storage_path('app/geo/zip_allCountries.zip');
        $filePath = storage_path('app/geo/allCountries.txt');
        $url = 'https://download.geonames.org/export/zip/allCountries.zip';

        // Завантажуємо ZIP-файл для поштових індексів
        $this->info("Downloading $url...");
        try {
            $this->client->get($url, ['sink' => $zipPath]);
            $this->info("Downloaded $zipPath successfully.");
        } catch (\Exception $e) {
            \Log::error("Error downloading $url: " . $e->getMessage());
            $this->error("Error downloading $url: " . $e->getMessage());
            return;
        }

        // Розпаковуємо ZIP
        try {
            $zip = new \ZipArchive;
            if ($zip->open($zipPath) === true) {
                $zip->extractTo(storage_path('app/geo'));
                $zip->close();
                $this->info("Extracted $zipPath successfully.");
            } else {
                \Log::error("Failed to extract $zipPath.");
                $this->error("Failed to extract $zipPath.");
                Storage::delete('geo/zip_allCountries.zip');
                return;
            }
        } catch (\Exception $e) {
            \Log::error("Error extracting $zipPath: " . $e->getMessage());
            $this->error("Error extracting $zipPath: " . $e->getMessage());
            Storage::delete('geo/zip_allCountries.zip');
            return;
        }

        if (!file_exists($filePath)) {
            $this->error("$filePath not found after extraction.");
            Storage::delete('geo/zip_allCountries.zip');
            return;
        }

        $countryCode = $this->option('country');
        $batchSize = 100;
        $zipcodesBatch = [];
        $start = microtime(true);

        $file = @fopen($filePath, 'r');
        if ($file === false) {
            $this->error("Failed to open $filePath.");
            Storage::delete(['geo/zip_allCountries.zip', 'geo/allCountries.txt']);
            return;
        }

        while (($line = fgets($file)) !== false) {
            $data = explode("\t", trim($line));
            if (count($data) < 3 || ($countryCode && $data[0] !== $countryCode)) {
                continue;
            }

            $zipcode = $data[1];
            $placeName = $data[2];
            $city = City::where('name', $placeName)->first();

            if ($city) {
                $zipcodesBatch[] = [
                    'zipcode' => $zipcode,
                    'city_id' => $city->id,
                    'minimum_free_delivery_order_amount' => 1000,
                    'delivery_charges' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($zipcodesBatch) >= $batchSize) {
                    $this->insertOrUpdateZipcodes($zipcodesBatch);
                    $zipcodesBatch = [];
                    gc_collect_cycles();
                    \Log::info('Memory usage after zipcodes batch', ['memory' => memory_get_usage() / 1024 / 1024 . ' MB']);
                    usleep(100000);
                }
            }
        }

        if (!empty($zipcodesBatch)) {
            $this->insertOrUpdateZipcodes($zipcodesBatch);
        }

        fclose($file);
        Storage::delete(['geo/zip_allCountries.zip', 'geo/allCountries.txt']);
        \Log::info('Zipcodes synced', ['count' => Zipcode::count(), 'time' => microtime(true) - $start]);
        $this->info('Zipcodes synced successfully in ' . (microtime(true) - $start) . ' seconds.');
    }

    protected function insertOrUpdateZipcodes(array $zipcodesBatch)
    {
        DB::transaction(function () use ($zipcodesBatch) {
            $zipcodes = array_column($zipcodesBatch, 'zipcode');
            $cityIds = array_column($zipcodesBatch, 'city_id');
            $existingZipcodes = Zipcode::whereIn('zipcode', $zipcodes)
                ->whereIn('city_id', $cityIds)
                ->pluck('zipcode', 'city_id')
                ->toArray();

            $newZipcodes = array_filter($zipcodesBatch, function ($zipcode) use ($existingZipcodes) {
                return !isset($existingZipcodes[$zipcode['city_id']][$zipcode['zipcode']]);
            });

            if (!empty($newZipcodes)) {
                Zipcode::insert($newZipcodes);
            }

            foreach ($zipcodesBatch as $zipcode) {
                if (isset($existingZipcodes[$zipcode['city_id']][$zipcode['zipcode']])) {
                    Zipcode::where('zipcode', $zipcode['zipcode'])
                        ->where('city_id', $zipcode['city_id'])
                        ->update([
                            'minimum_free_delivery_order_amount' => $zipcode['minimum_free_delivery_order_amount'],
                            'delivery_charges' => $zipcode['delivery_charges'],
                            'updated_at' => $zipcode['updated_at'],
                        ]);
                }
            }
        }, 3);
    }
}
