<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ShippingService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.rajaongkir.key', '');
        $this->baseUrl = (string) config('services.rajaongkir.base_url', '');
    }

    /**
    * Ambil daftar provinsi.
    * Jika tidak ada API key, kembalikan data dummy.
    */
    public function getProvinces(): Collection
    {
        if ($this->apiKey === '' || $this->baseUrl === '') {
            return collect([
                ['id' => '1', 'name' => 'DKI Jakarta'],
                ['id' => '2', 'name' => 'Jawa Barat'],
            ]);
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->get($this->baseUrl . '/province');

        if ($response->failed()) {
            return collect();
        }

        return collect($response->json('rajaongkir.results') ?? [])->map(function ($prov) {
            return [
                'id' => $prov['province_id'],
                'name' => $prov['province'],
            ];
        });
    }

    /**
    * Ambil daftar kota berdasarkan ID provinsi.
    * Jika tidak ada API key, kembalikan data dummy.
    */
    public function getCities(string $provinceId): Collection
    {
        if ($this->apiKey === '' || $this->baseUrl === '') {
            return collect([
                ['id' => '501', 'name' => 'Jakarta Selatan'],
                ['id' => '502', 'name' => 'Jakarta Barat'],
            ]);
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->get($this->baseUrl . '/city', [
            'province' => $provinceId,
        ]);

        if ($response->failed()) {
            return collect();
        }

        return collect($response->json('rajaongkir.results') ?? [])->map(function ($city) {
            return [
                'id' => $city['city_id'],
                'name' => $city['city_name'],
            ];
        });
    }

    /**
    * Hitung ongkir.
    * Jika tidak ada API key, kembalikan opsi dummy.
    */
    public function calculateShipping(string $origin, string $destination, int $weight, string $courier): Collection
    {
        if ($this->apiKey === '' || $this->baseUrl === '') {
            return collect([
                [
                    'service' => 'REG',
                    'description' => 'Regular Service',
                    'cost' => 20000,
                    'etd' => '2-3',
                ],
                [
                    'service' => 'YES',
                    'description' => 'Yakin Esok Sampai',
                    'cost' => 35000,
                    'etd' => '1',
                ],
            ]);
        }

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->post($this->baseUrl . '/cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);

        if ($response->failed()) {
            return collect();
        }

        $costs = $response->json('rajaongkir.results.0.costs') ?? [];

        return collect($costs)->map(function ($item) {
            $firstCost = $item['cost'][0] ?? [];

            return [
                'service' => $item['service'] ?? '',
                'description' => $item['description'] ?? '',
                'cost' => $firstCost['value'] ?? 0,
                'etd' => $firstCost['etd'] ?? '',
            ];
        });
    }
}

