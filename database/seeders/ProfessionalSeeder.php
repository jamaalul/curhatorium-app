<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professional;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professionals = [
            [
                'name' => 'Dr. Siti Rahma',
                'title' => 'Psychiatrist',
                'avatar' => 'avatars/siti_rahma.png',
                'specialties' => json_encode(['Anxiety', 'Depression']),
                'availability' => 'online',
                'availabilityText' => 'Tersedia',
                'type' => 'psychiatrist',
                'rating' => 4.8,
                'whatsapp_number' => '088989406047',
                'bank_account_number' => '1234567890',
                'bank_name' => 'BCA',
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'title' => 'Psychiatrist',
                'avatar' => 'avatars/budi_santoso.png',
                'specialties' => json_encode(['Bipolar', 'Schizophrenia']),
                'availability' => 'offline',
                'availabilityText' => 'Offline',
                'type' => 'psychiatrist',
                'rating' => 4.5,
                'whatsapp_number' => '088989406047',
                'bank_account_number' => '9876543210',
                'bank_name' => 'Mandiri',
            ],
            [
                'name' => 'Dewi Lestari',
                'title' => 'Partner',
                'avatar' => 'avatars/dewi_lestari.png',
                'specialties' => json_encode(['Stress Management', 'Self-esteem']),
                'availability' => 'busy',
                'availabilityText' => 'Sedang dalam sesi',
                'type' => 'partner',
                'rating' => 4.7,
                'whatsapp_number' => '088989406047',
                'bank_account_number' => '1122334455',
                'bank_name' => 'BNI',
            ],
        ];

        foreach ($professionals as $professional) {
            // specialties is cast as array, so decode before create
            $professional['specialties'] = json_decode($professional['specialties']);
            Professional::create($professional);
        }
    }
} 