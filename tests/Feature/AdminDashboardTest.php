<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_operational_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mechanic = User::factory()->create(['role' => 'mechanic']);
        $customer = User::factory()->create(['role' => 'customer']);

        ServiceBooking::create([
            'service_code' => 'RS-TST-0001',
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'vehicle' => 'Toyota Vios 2020',
            'service_type' => 'Brake Repair',
            'status' => 'completed',
            'scheduled_at' => now(),
            'labor_cost' => 1500,
            'parts_cost' => 900,
        ]);

        SparePart::create([
            'sku' => 'TST-BRK-001',
            'name' => 'Test Brake Pads',
            'category' => 'Brakes',
            'quantity' => 2,
            'reorder_level' => 5,
            'unit_cost' => 600,
            'price' => 950,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('RideSync Command Center')
            ->assertSee(route('admin.bookings.index', absolute: false))
            ->assertSee(route('admin.assignments', absolute: false))
            ->assertSee(route('admin.inventory', absolute: false))
            ->assertSee(route('admin.mechanics.index', absolute: false))
            ->assertSee(route('admin.customers.index', absolute: false))
            ->assertSee(route('admin.reports', absolute: false))
            ->assertSee(route('admin.audit', absolute: false))
            ->assertSee('PHP 2,400.00');

        $this->actingAs($admin)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('Job Management')
            ->assertSee('RS-TST-0001');

        $this->actingAs($admin)
            ->get(route('admin.inventory'))
            ->assertOk()
            ->assertSee('Inventory Management')
            ->assertSee('Test Brake Pads');

        $this->actingAs($admin)
            ->get(route('admin.mechanics.index'))
            ->assertOk()
            ->assertSee('Mechanic Management')
            ->assertSee($mechanic->email);

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Customer Management')
            ->assertSee($customer->email)
            ->assertSee('Brake Repair')
            ->assertSee('Completed')
            ->assertDontSee('Add Customer');

        $this->actingAs($admin)
            ->get(route('admin.assignments'))
            ->assertOk()
            ->assertSee('Assignments')
            ->assertSee('RS-TST-0001');

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('Reports')
            ->assertSee('PHP 2,400.00');

        $this->actingAs($admin)
            ->get(route('admin.audit'))
            ->assertOk()
            ->assertSee('Audit Logs');
    }

    public function test_admin_can_manage_jobs_inventory_people_and_audit_logs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mechanic = User::factory()->create(['role' => 'mechanic']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin);

        $this->post(route('admin.bookings.store'), [
            'customer_id' => $customer->id,
            'mechanic_id' => '',
            'customer_name' => '',
            'customer_email' => '',
            'customer_phone' => '0917 000 0000',
            'vehicle' => 'Honda City 2019',
            'service_type' => 'Engine Diagnostics',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'labor_cost' => 800,
            'parts_cost' => 0,
            'notes' => 'Dashboard verification job.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $booking = ServiceBooking::firstOrFail();
        $this->assertSame('pending', $booking->status);

        $this->patch(route('admin.bookings.assign', $booking), [
            'mechanic_id' => $mechanic->id,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $booking->refresh();
        $this->assertSame($mechanic->id, $booking->mechanic_id);
        $this->assertSame('assigned', $booking->status);

        $this->patch(route('admin.bookings.status', $booking), [
            'status' => 'completed',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->put(route('admin.bookings.update', $booking), [
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'customer_name' => '',
            'customer_email' => '',
            'customer_phone' => '0917 000 0000',
            'vehicle' => 'Honda City 2020',
            'service_type' => 'Engine Diagnostics',
            'status' => 'completed',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'labor_cost' => 1000,
            'parts_cost' => 250,
            'notes' => 'Updated dashboard verification job.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('service_bookings', [
            'id' => $booking->id,
            'vehicle' => 'Honda City 2020',
            'status' => 'completed',
        ]);

        $this->post(route('admin.parts.store'), [
            'sku' => 'OIL-TST-001',
            'name' => 'Verification Oil',
            'category' => 'Fluids',
            'quantity' => 10,
            'reorder_level' => 4,
            'unit_cost' => 250,
            'price' => 400,
            'supplier' => 'Test Supplier',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $part = SparePart::firstOrFail();
        $this->put(route('admin.parts.update', $part), [
            'sku' => 'OIL-TST-001',
            'name' => 'Verification Oil Plus',
            'category' => 'Fluids',
            'quantity' => 7,
            'reorder_level' => 4,
            'unit_cost' => 260,
            'price' => 420,
            'supplier' => 'Test Supplier',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('spare_parts', ['id' => $part->id, 'name' => 'Verification Oil Plus']);

        $this->post(route('admin.mechanics.store'), [
            'name' => 'New Mechanic',
            'email' => 'new.mechanic@example.test',
            'password' => 'Mechanic@123',
            'password_confirmation' => 'Mechanic@123',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $newMechanic = User::where('email', 'new.mechanic@example.test')->firstOrFail();
        $this->put(route('admin.mechanics.update', $newMechanic), [
            'name' => 'Updated Mechanic',
            'email' => 'updated.mechanic@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $newMechanic->id, 'name' => 'Updated Mechanic']);

        $this->put(route('admin.customers.update', $customer), [
            'name' => 'Updated Customer',
            'email' => 'updated.customer@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'Updated Customer']);

        $this->assertGreaterThanOrEqual(9, AuditLog::count());

        $this->delete(route('admin.parts.destroy', $part))->assertRedirect();
        $this->delete(route('admin.bookings.destroy', $booking))->assertRedirect();
        $this->delete(route('admin.mechanics.destroy', $newMechanic->fresh()))->assertRedirect();
        $this->delete(route('admin.customers.destroy', $customer->fresh()))->assertRedirect();

        $this->assertDatabaseMissing('spare_parts', ['id' => $part->id]);
        $this->assertDatabaseMissing('service_bookings', ['id' => $booking->id]);
        $this->assertDatabaseMissing('users', ['id' => $newMechanic->id]);
        $this->assertDatabaseMissing('users', ['id' => $customer->id]);
    }

    public function test_mechanics_cannot_register_from_public_registration(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Customer')
            ->assertDontSee('Mechanic');

        $this->post(route('register.store'), [
            'name' => 'Public Mechanic',
            'email' => 'public.mechanic@example.test',
            'role' => 'mechanic',
            'birthday' => '1998-06-15',
            'password' => 'Mechanic@123',
            'password_confirmation' => 'Mechanic@123',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'public.mechanic@example.test',
        ]);
    }

    public function test_public_registration_saves_customer_birthday(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Public Customer',
            'email' => 'public.customer@example.test',
            'role' => 'customer',
            'birthday' => '1997-04-21',
            'password' => 'Customer@123',
            'password_confirmation' => 'Customer@123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login', absolute: false));

        $customer = User::where('email', 'public.customer@example.test')->firstOrFail();

        $this->assertSame('Public Customer', $customer->name);
        $this->assertSame('customer', $customer->role);
        $this->assertSame('1997-04-21', $customer->birthday->toDateString());
    }

    public function test_admin_login_logs_and_profile_page_work(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('Admin@123'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'admin@example.test',
            'password' => 'Admin@123',
        ])->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertDatabaseHas('login_logs', [
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'profile.verified',
            'summary' => 'Verified admin profile logs.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Login Logs')
            ->assertSee('Audit History')
            ->assertSee('Verified admin profile logs.');

        $this->assertSame(1, LoginLog::count());
    }
}
