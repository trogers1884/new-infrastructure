<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class InitialStoreSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function() {
            // Clear existing assignments to prevent duplicates
            Store::truncate();

            Store::create([
                'name' => 'MW BMW',
                'code' => '101',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'MW Honda',
                'code' => '102',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'MW Infiniti',
                'code' => '103',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'MW Mercedes Benz',
                'code' => '104',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'MW Porche',
                'code' => '105',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'MW Cadillac',
                'code' => '106',
                'address' => '1475 S. Barrington Rd.',
                'city' => 'Barrington',
                'state' => 'IL',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Stuart - Alfa Romero',
                'code' => '10',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Central New Jersey - Ferrari',
                'code' => '11',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Jacksonville - Bentley',
                'code' => '12',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Central New Jersey - Maserati',
                'code' => '13',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Jacksonville - Maserati',
                'code' => '14',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Jacksonville - Bentley',
                'code' => '16',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Libertyville - Honda',
                'code' => '17',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Highland Park - Acura',
                'code' => '18',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Stuart -Audi',
                'code' => '2',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Richmond - Mercedes Benz',
                'code' => '21',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Midlotian - Mercedes Benz',
                'code' => '22',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Stuart - Infiniti',
                'code' => '3',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Brickell Honda',
                'code' => '4',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Brickell Mazda',
                'code' => '5',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Ocean Cadillac',
                'code' => '6',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Chicago - Honda',
                'code' => '7',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Chicago - Volkswagen',
                'code' => '8',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'Sturat - Maserati',
                'code' => '9',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'OldMw - Jaguar',
                'code' => '-10',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'OldMw - Landrover',
                'code' => '-9',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'OldMw - Ininiti HE',
                'code' => '-8',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
            Store::create([
                'name' => 'OldMw - Mercedes Benz HE',
                'code' => '-7',
                'address' => '',
                'city' => '',
                'state' => '',
                'in_service_date' => now(),
            ]);
        });
    }

}
