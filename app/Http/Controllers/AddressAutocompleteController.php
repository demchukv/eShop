<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Models\Zipcode;
use Carbon\Carbon;

class AddressAutocompleteController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://wft-geo-db.p.rapidapi.com/',
            'headers' => [
                'x-rapidapi-key' => env('GEODB_API_KEY'),
                'x-rapidapi-host' => 'wft-geo-db.p.rapidapi.com',
            ],
        ]);
    }

    /**
     * Get countries for autocomplete.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries(Request $request)
    {
        $request->validate(['q' => 'required|string|min:1|max:255']);
        $query = $request->input('q');

        $countries = Country::where('name', 'LIKE', $query . '%')
            ->select('id', 'name AS text', 'iso2')
            ->limit(10)
            ->get();

        return response()->json(['results' => $countries]);
    }

    /**
     * Get regions for autocomplete based on country ID and query.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegions(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'q' => 'required|string|min:1|max:255',
        ]);

        $countryId = $request->input('country_id');
        $query = $request->input('q');
        $country = Country::findOrFail($countryId);
        $iso2 = $country->iso2;

        // Search by both name and native_name
        $regions = Region::where('country_id', $countryId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', $query . '%')
                    ->orWhere('native_name', 'LIKE', $query . '%');
            })
            ->select('id', 'name AS text', 'native_name')
            ->limit(10)
            ->get();

        // If no regions or data is stale (>30 days), fetch from API
        if ($regions->isEmpty() || $this->isDataStale($countryId, 'regions')) {
            try {
                $offset = 0;
                $limit = 10;
                $regionsBatch = [];

                // Fetch English names
                $responseEn = $this->client->get("v1/geo/countries/{$iso2}/regions", [
                    'query' => [
                        'limit' => $limit,
                        'offset' => $offset,
                        'namePrefix' => $query,
                        'languageCode' => 'en',
                    ],
                ]);
                $dataEn = json_decode($responseEn->getBody(), true);
                $regionsEn = $dataEn['data'] ?? [];

                // sleep(2);

                // Fetch native names
                // $languageCode = $iso2 === 'UA' ? 'uk' : 'en';
                // $responseNative = $this->client->get("v1/geo/countries/{$iso2}/regions", [
                //     'query' => [
                //         'limit' => $limit,
                //         'offset' => $offset,
                //         'namePrefix' => $query,
                //         'languageCode' => $languageCode,
                //     ],
                // ]);
                // $dataNative = json_decode($responseNative->getBody(), true);
                // \Log::debug(json_encode($dataNative));
                // $regionsNative = $dataNative['data'] ?? [];

                $totalCount = $dataEn['metadata']['totalCount'] ?? 0;

                foreach ($regionsEn as $index => $regionEn) {
                    // $nativeName = $regionsNative[$index]['name'] ?? $regionEn['name'];
                    $regionsBatch[] = [
                        'name' => $regionEn['name'],
                        // 'native_name' => $nativeName !== $regionEn['name'] ? $nativeName : null,
                        'admin1_code' => $regionEn['isoCode'] ?? null,
                        'country_id' => $countryId,
                        'minimum_free_delivery_order_amount' => 0.00,
                        'delivery_charges' => 0.00,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($regionsBatch)) {
                    $this->insertOrUpdateRegions($regionsBatch);
                }

                // Re-query after API fetch
                $regions = Region::where('country_id', $countryId)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', $query . '%')
                            ->orWhere('native_name', 'LIKE', $query . '%');
                    })
                    ->select('id', 'name AS text', 'native_name')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                \Log::error("Error fetching regions for {$iso2}: " . $e->getMessage());
                return response()->json(['error' => 'Failed to fetch regions'], 500);
            }
        }

        return response()->json(['results' => $regions]);
    }

    /**
     * Get cities for autocomplete based on country ID, region ID (optional), and query.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'q' => 'required|string|min:1|max:255',
        ]);

        $countryId = $request->input('country_id');
        $regionId = $request->input('region_id');
        $query = $request->input('q');
        $country = Country::findOrFail($countryId);
        $iso2 = $country->iso2;
        $region = Region::findOrFail($regionId);
        $admin1Code = $region->admin1_code;

        // Build query for local database
        $citiesQuery = City::where('country_id', $countryId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', $query . '%')
                    ->orWhere('native_name', 'LIKE', $query . '%');
            });

        if ($regionId) {
            $citiesQuery->where('region_id', $regionId);
        }

        $cities = $citiesQuery->select('id', 'name AS text', 'native_name')
            ->limit(10)
            ->get();

        // If no cities or data is stale (>30 days), fetch from API
        if ($cities->isEmpty() || $this->isDataStale($countryId, 'cities', $regionId)) {
            try {
                $offset = 0;
                $limit = 10;
                $citiesBatch = [];
                // $regions = Region::where('country_id', $countryId)->pluck('id', 'admin1_code');

                $endpoint = $regionId
                    ? "v1/geo/countries/{$iso2}/regions/{$admin1Code}/cities"
                    : "v1/geo/countries/{$iso2}/cities";

                // Fetch English names
                $responseEn = $this->client->get($endpoint, [
                    'query' => [
                        'minPopulation' => 1000,
                        'namePrefix' => $query,
                        'limit' => $limit,
                        'offset' => $offset,
                        'languageCode' => 'en',
                    ],
                ]);
                $dataEn = json_decode($responseEn->getBody(), true);
                $citiesEn = $dataEn['data'] ?? [];

                // Fetch native names
                // $languageCode = $iso2 === 'UA' ? 'uk' : 'en';
                // $responseNative = $this->client->get($endpoint, [
                //     'query' => [
                //         'minPopulation' => 1000,
                //         'namePrefix' => $query,
                //         'limit' => $limit,
                //         'offset' => $offset,
                //         'languageCode' => $languageCode,
                //     ],
                // ]);
                // $dataNative = json_decode($responseNative->getBody(), true);
                // $citiesNative = $dataNative['data'] ?? [];

                $totalCount = $dataEn['metadata']['totalCount'] ?? 0;

                foreach ($citiesEn as $index => $cityEn) {
                    $nativeName = $citiesNative[$index]['name'] ?? $cityEn['name'];
                    $citiesBatch[] = [
                        'name' => $cityEn['name'],
                        // 'native_name' => $nativeName !== $cityEn['name'] ? $nativeName : null,
                        'country_id' => $countryId,
                        'region_id' => $regionId,
                        'minimum_free_delivery_order_amount' => 0.00,
                        'delivery_charges' => 0.00,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($citiesBatch)) {
                    $this->insertOrUpdateCities($citiesBatch);
                }

                // Re-query after API fetch
                $cities = $citiesQuery->select('id', 'name AS text', 'native_name')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                \Log::error("Error fetching cities for {$iso2}: " . $e->getMessage());
                return response()->json(['error' => 'Failed to fetch cities'], 500);
            }
        }

        return response()->json(['results' => $cities]);
    }

    /**
     * Get zipcodes for autocomplete based on city ID and query.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getZipcodes(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'q' => 'required|string|min:1|max:255',
        ]);

        $cityId = $request->input('city_id');
        $query = $request->input('q');
        $city = City::findOrFail($cityId);
        $countryId = $city->country_id;
        $country = Country::findOrFail($countryId);
        $iso2 = $country->iso2;
        \Log::debug('ISO2 = ' . $iso2);
        // Check local database
        $zipcodes = Zipcode::where('city_id', $cityId)
            ->where('zipcode', 'LIKE', $query . '%')
            ->select('id', 'zipcode AS text')
            ->limit(10)
            ->get();

        // If no zipcodes or data is stale (>30 days), fetch from GeoNames
        if ($zipcodes->isEmpty() || $this->isDataStale($cityId, 'zipcodes')) {
            try {
                $zipPath = storage_path("app/geo/{$iso2}.zip");
                $filePath = storage_path("app/geo/{$iso2}.txt");
                $url = "https://download.geonames.org/export/zip/{$iso2}.zip";
                \Log::debug('ZIP CODE URL = ' . $url);
                // Download and extract GeoNames ZIP file if needed
                if (!file_exists($filePath)) {
                    Storage::makeDirectory('geo');
                    $this->client->get($url, ['sink' => $zipPath]);
                    $zip = new \ZipArchive;
                    if ($zip->open($zipPath) !== true) {
                        throw new \Exception("Failed to extract $zipPath");
                    }
                    $zip->extractTo(storage_path('app/geo'));
                    $zip->close();
                }

                $file = @fopen($filePath, 'r');
                if ($file === false) {
                    throw new \Exception("Failed to open $filePath");
                }

                $zipcodesBatch = [];
                while (($line = fgets($file)) !== false) {
                    $data = explode("\t", trim($line));
                    //["UA","79049","\u041b\u044c\u0432\u0456\u0432","Lvivska","15","Lvivska","","","","49.8383","24.0232","4"]
                    // \Log::debug(json_encode($data));
                    if (count($data) < 3 || $data[0] !== $iso2) {
                        continue;
                    }

                    $zipcodesBatch[] = [
                        'zipcode' => $data[1],
                        'city_id' => $cityId,
                        'minimum_free_delivery_order_amount' => 0.00,
                        'delivery_charges' => 0.00,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                fclose($file);
                Storage::delete(["geo/{$iso2}.zip", "geo/{$iso2}.txt"]);

                // Process zipcodes in chunks of 500
                if (!empty($zipcodesBatch)) {
                    $chunks = array_chunk($zipcodesBatch, 1000);
                    foreach ($chunks as $index => $chunk) {
                        // \Log::info("Processing zipcodes chunk", ['chunk' => $index + 1, 'size' => count($chunk)]);
                        $this->insertOrUpdateZipcodes($chunk);
                    }
                }

                // Re-query after fetching
                $zipcodes = Zipcode::where('city_id', $cityId)
                    ->where('zipcode', 'LIKE', $query . '%')
                    ->select('id', 'zipcode AS text')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                \Log::error("Error fetching zipcodes for city {$cityId}: " . $e->getMessage());
                return response()->json(['error' => 'Failed to fetch zipcodes'], 500);
            }
        }

        return response()->json(['results' => $zipcodes]);
    }

    /**
     * Check if data is stale (older than 30 days).
     *
     * @param int $id
     * @param string $type
     * @param int|null $secondaryId
     * @return bool
     */
    protected function isDataStale($id, $type, $secondaryId = null)
    {
        $model = match ($type) {
            'regions' => Region::where('country_id', $id),
            'cities' => $secondaryId ? City::where('region_id', $secondaryId) : City::where('country_id', $id),
            'zipcodes' => Zipcode::where('city_id', $id),
            default => null,
        };

        if (!$model) {
            return true;
        }

        $latest = $model->orderBy('updated_at', 'desc')->first();
        return !$latest || $latest->updated_at->diffInDays(now()) > 30;
    }

    /**
     * Insert or update regions in the database.
     *
     * @param array $regionsBatch
     * @return void
     */
    protected function insertOrUpdateRegions(array $regionsBatch)
    {
        DB::transaction(function () use ($regionsBatch) {
            $names = array_column($regionsBatch, 'name');
            $countryIds = array_column($regionsBatch, 'country_id');
            $existingRegions = Region::whereIn('name', $names)
                ->whereIn('country_id', $countryIds)
                ->pluck('name', 'country_id')
                ->toArray();

            $newRegions = array_filter($regionsBatch, function ($region) use ($existingRegions) {
                return !isset($existingRegions[$region['country_id']][$region['name']]);
            });

            if (!empty($newRegions)) {
                Region::insert($newRegions);
            }

            foreach ($regionsBatch as $region) {
                if (isset($existingRegions[$region['country_id']][$region['name']])) {
                    Region::where('name', $region['name'])
                        ->where('country_id', $region['country_id'])
                        ->update([
                            'native_name' => $region['native_name'],
                            'minimum_free_delivery_order_amount' => $region['minimum_free_delivery_order_amount'],
                            'delivery_charges' => $region['delivery_charges'],
                            'updated_at' => $region['updated_at'],
                        ]);
                }
            }
        }, 3);
    }

    /**
     * Insert or update cities in the database.
     *
     * @param array $citiesBatch
     * @return void
     */
    protected function insertOrUpdateCities(array $citiesBatch)
    {
        DB::transaction(function () use ($citiesBatch) {
            $names = array_column($citiesBatch, 'name');
            $countryIds = array_column($citiesBatch, 'country_id');
            $existingCities = City::whereIn('name', $names)
                ->whereIn('country_id', $countryIds)
                ->pluck('name', 'country_id')
                ->toArray();

            $newCities = array_filter($citiesBatch, function ($city) use ($existingCities) {
                return !isset($existingCities[$city['country_id']][$city['name']]);
            });

            if (!empty($newCities)) {
                City::insert($newCities);
            }

            foreach ($citiesBatch as $city) {
                if (isset($existingCities[$city['country_id']][$city['name']])) {
                    City::where('name', $city['name'])
                        ->where('country_id', $city['country_id'])
                        ->update([
                            'native_name' => $city['native_name'],
                            'minimum_free_delivery_order_amount' => $city['minimum_free_delivery_order_amount'],
                            'delivery_charges' => $city['delivery_charges'],
                            'updated_at' => $city['updated_at'],
                        ]);
                }
            }
        }, 3);
    }

    /**
     * Insert or update zipcodes in the database.
     *
     * @param array $zipcodesBatch
     * @return void
     */
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
