<?php

namespace Database\Seeders;

use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'role' => 'admin',
        ], [
            'name' => config('ridesync.admin.name'),
            'email' => config('ridesync.admin.email'),
            'password' => Hash::make(config('ridesync.admin.password')),
        ]);

        $mechanic = User::updateOrCreate([
            'email' => 'mechanic@ridesync.test',
        ], [
            'name' => 'RideSync Mechanic',
            'role' => 'mechanic',
            'password' => Hash::make('Mechanic@123'),
        ]);

        $customer = User::updateOrCreate([
            'email' => 'customer@ridesync.test',
        ], [
            'name' => 'RideSync Customer',
            'role' => 'customer',
            'password' => Hash::make('Customer@123'),
        ]);

        SparePart::updateOrCreate([
            'sku' => 'BRK-PAD-001',
        ], [
            'name' => 'Ceramic Brake Pad Set',
            'category' => 'Brakes',
            'quantity' => 8,
            'reorder_level' => 5,
            'unit_cost' => 850,
            'price' => 1450,
            'supplier' => 'Metro Auto Supply',
        ]);

        SparePart::updateOrCreate([
            'sku' => 'OIL-5W30-004',
        ], [
            'name' => 'Synthetic Oil 5W-30',
            'category' => 'Fluids',
            'quantity' => 3,
            'reorder_level' => 6,
            'unit_cost' => 320,
            'price' => 520,
            'supplier' => 'Luzon Parts Depot',
        ]);

        ServiceBooking::updateOrCreate([
            'service_code' => 'RS-DEMO-0001',
        ], [
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => '0917 555 0123',
            'vehicle' => 'Toyota Vios 2020',
            'service_type' => 'Brake Repair',
            'status' => 'completed',
            'scheduled_at' => now()->subMonth()->setTime(10, 0),
            'labor_cost' => 1800,
            'parts_cost' => 1450,
            'notes' => 'Front brake pads replaced and road-tested.',
        ]);

        ServiceBooking::updateOrCreate([
            'service_code' => 'RS-DEMO-0002',
        ], [
            'customer_id' => $customer->id,
            'mechanic_id' => null,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => '0917 555 0123',
            'vehicle' => 'Honda City 2019',
            'service_type' => 'Engine Diagnostics',
            'status' => 'pending',
            'scheduled_at' => now()->addDay()->setTime(14, 0),
            'labor_cost' => 900,
            'parts_cost' => 0,
            'notes' => 'Customer reported intermittent check engine light.',
        ]);
    }
}
