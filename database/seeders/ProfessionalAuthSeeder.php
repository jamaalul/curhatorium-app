<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Professional;
use Illuminate\Support\Facades\Hash;

class ProfessionalAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing professionals with proper authentication credentials
        $professionals = Professional::all();
        
        foreach ($professionals as $professional) {
            // Only set password for professionals with WhatsApp numbers
            if ($professional->whatsapp_number && !$professional->password) {
                $professional->password = Hash::make('password123'); // Default password
                $professional->save();
            }
        }
        
        $this->command->info('Professional authentication credentials updated successfully!');
        $this->command->info('Default password for all professionals: password123');
        $this->command->info('Login using your WhatsApp number and password.');
        $this->command->info('Please change passwords after first login for security.');
    }
}
