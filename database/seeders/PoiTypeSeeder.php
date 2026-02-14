<?php

namespace Database\Seeders;

use App\Models\PoiType;
use Illuminate\Database\Seeder;

class PoiTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Maktab', 'icon' => 'school'],
            ['name' => 'Masjid', 'icon' => 'mosque'],
            ['name' => 'Kasalxona', 'icon' => 'hospital'],
            ['name' => 'Dorixona', 'icon' => 'pharmacy'],
            ['name' => "Do'kon", 'icon' => 'shop'],
            ["name" => "Bog'cha", 'icon' => 'kindergarten'],
            ['name' => 'Park', 'icon' => 'park'],
            ['name' => 'Bozor', 'icon' => 'market'],
            ['name' => 'Idora', 'icon' => 'office'],
            ['name' => 'Sport maydoni', 'icon' => 'sport'],
            ['name' => 'Kutubxona', 'icon' => 'library'],
            ['name' => 'Oshxona', 'icon' => 'restaurant'],
            ['name' => 'Bank', 'icon' => 'bank'],
            ['name' => 'Pochta', 'icon' => 'post'],
        ];

        foreach ($types as $type) {
            PoiType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
