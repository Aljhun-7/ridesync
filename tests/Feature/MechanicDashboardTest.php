<?php

namespace Tests\Feature;

use App\Models\RepairPartUsage;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MechanicDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_mechanic_dashboard_shows_assigned_repairs_and_metrics(): void
    {
        $mechanic = User::factory()->create(['role' => 'mechanic']);
        $otherMechanic = User::factory()->create(['role' => 'mechanic']);
        $customer = User::factory()->create(['role' => 'customer']);

        ServiceBooking::create([
            'service_code' => 'RS-MECH-001',
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'vehicle' => 'Toyota Vios 2020',
            'service_type' => 'Brake Repair',
            'status' => 'in_progress',
            'scheduled_at' => now(),
            'labor_cost' => 1500,
            'parts_cost' => 0,
        ]);

        ServiceBooking::create([
            'service_code' => 'RS-MECH-002',
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => $customer->name,
            'vehicle' => 'Oil Change',
            'service_type' => 'Oil Change',
            'status' => 'completed',
            'scheduled_at' => now()->subDay(),
            'labor_cost' => 800,
            'parts_cost' => 300,
        ]);

        ServiceBooking::create([
            'service_code' => 'RS-MECH-OTHER',
            'customer_id' => $customer->id,
            'mechanic_id' => $otherMechanic->id,
            'customer_name' => $customer->name,
            'vehicle' => 'Honda City 2019',
            'service_type' => 'Diagnostics',
            'status' => 'assigned',
        ]);

        $this->actingAs($mechanic)
            ->get(route('mechanic.dashboard'))
            ->assertOk()
            ->assertSee('Assigned Repair Dashboard')
            ->assertSee('My Repairs')
            ->assertSee('Performance Metrics')
            ->assertSee('PHP 800.00')
            ->assertSee('RS-MECH-001')
            ->assertSee('RS-MECH-002')
            ->assertDontSee('RS-MECH-OTHER');

        $this->actingAs($mechanic)
            ->get(route('mechanic.repairs.index', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('RS-MECH-002')
            ->assertDontSee('RS-MECH-001');
    }

    public function test_mechanic_can_update_repair_and_track_parts_used(): void
    {
        $mechanic = User::factory()->create(['role' => 'mechanic']);
        $customer = User::factory()->create(['role' => 'customer']);
        $part = SparePart::create([
            'sku' => 'BRK-PAD-TST',
            'name' => 'Brake Pads',
            'category' => 'Brakes',
            'quantity' => 5,
            'reorder_level' => 1,
            'unit_cost' => 500,
            'price' => 900,
        ]);
        $booking = ServiceBooking::create([
            'service_code' => 'RS-WORK-001',
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => $customer->name,
            'vehicle' => 'Toyota Vios 2020',
            'service_type' => 'Brake Repair',
            'status' => 'assigned',
            'labor_cost' => 1200,
            'parts_cost' => 0,
        ]);

        $this->actingAs($mechanic)
            ->patch(route('mechanic.repairs.work', $booking), [
                'status' => 'completed',
                'notes' => 'Installed pads and road-tested the vehicle.',
                'spare_part_id' => $part->id,
                'quantity' => 2,
            ])->assertSessionHasNoErrors()->assertRedirect();

        $booking->refresh();
        $part->refresh();

        $this->assertSame('completed', $booking->status);
        $this->assertSame('Installed pads and road-tested the vehicle.', $booking->notes);
        $this->assertSame('1800.00', $booking->parts_cost);
        $this->assertSame(3, $part->quantity);
        $this->assertSame(1, RepairPartUsage::count());
    }

    public function test_mechanic_cannot_update_unassigned_repair(): void
    {
        $mechanic = User::factory()->create(['role' => 'mechanic']);
        $otherMechanic = User::factory()->create(['role' => 'mechanic']);
        $booking = ServiceBooking::create([
            'service_code' => 'RS-WORK-LOCKED',
            'mechanic_id' => $otherMechanic->id,
            'customer_name' => 'Locked Customer',
            'vehicle' => 'Honda City 2019',
            'service_type' => 'Diagnostics',
            'status' => 'assigned',
        ]);

        $this->actingAs($mechanic)
            ->patch(route('mechanic.repairs.work', $booking), [
                'status' => 'in_progress',
                'notes' => 'Trying to edit another repair.',
            ])->assertNotFound();
    }
}
