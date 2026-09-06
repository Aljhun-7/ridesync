<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\RepairPartUsage;
use App\Models\ServiceBooking;
use App\Models\SparePart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->role.'.dashboard');
    }

    public function admin(): View
    {
        return view('dashboards.admin', $this->adminDashboardData());
    }

    public function adminBookings(): View
    {
        return view('dashboards.admin.bookings', $this->adminDashboardData());
    }

    public function adminAssignments(): View
    {
        return view('dashboards.admin.assignments', $this->adminDashboardData());
    }

    public function adminInventory(): View
    {
        return view('dashboards.admin.inventory', $this->adminDashboardData());
    }

    public function adminMechanics(): View
    {
        return view('dashboards.admin.users', [
            ...$this->adminDashboardData(),
            'rolePage' => [
                'title' => 'Mechanic Management',
                'subtitle' => 'Add, edit, and delete mechanic records',
                'records' => User::where('role', 'mechanic')->orderBy('name')->get(),
                'store' => route('admin.mechanics.store'),
                'update' => 'admin.mechanics.update',
                'delete' => 'admin.mechanics.destroy',
                'singular' => 'Mechanic',
                'allowCreate' => true,
                'showRepairs' => false,
            ],
        ]);
    }

    public function adminCustomers(): View
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $customerRepairs = ServiceBooking::with('mechanic')
            ->whereIn('customer_id', $customers->pluck('id'))
            ->latest('scheduled_at')
            ->latest()
            ->get()
            ->groupBy('customer_id');

        return view('dashboards.admin.users', [
            ...$this->adminDashboardData(),
            'rolePage' => [
                'title' => 'Customer Management',
                'subtitle' => 'Manage customer records and repair status',
                'records' => $customers,
                'update' => 'admin.customers.update',
                'delete' => 'admin.customers.destroy',
                'singular' => 'Customer',
                'allowCreate' => false,
                'showRepairs' => true,
                'repairs' => $customerRepairs,
            ],
        ]);
    }

    public function adminReports(): View
    {
        return view('dashboards.admin.reports', $this->adminDashboardData());
    }

    public function adminAuditLogs(): View
    {
        return view('dashboards.admin.audit', $this->adminDashboardData());
    }

    /**
     * @return array<string, mixed>
     */
    private function adminDashboardData(): array
    {
        $bookings = ServiceBooking::with(['customer', 'mechanic'])
            ->latest('scheduled_at')
            ->latest()
            ->get();
        $mechanics = User::where('role', 'mechanic')->orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $parts = SparePart::orderBy('name')->get();
        $auditLogs = AuditLog::with('user')->latest()->limit(12)->get();

        $completedBookings = $bookings->where('status', 'completed');
        $totalRevenue = $completedBookings->sum(fn (ServiceBooking $booking): float => $booking->total);
        $monthlyRevenue = $completedBookings
            ->filter(fn (ServiceBooking $booking): bool => $booking->scheduled_at !== null)
            ->groupBy(fn (ServiceBooking $booking): string => $booking->scheduled_at->format('M Y'))
            ->map(fn ($items): float => $items->sum(fn (ServiceBooking $booking): float => $booking->total));
        $serviceRevenue = $completedBookings
            ->groupBy('service_type')
            ->map(fn ($items): float => $items->sum(fn (ServiceBooking $booking): float => $booking->total))
            ->sortDesc();
        $mechanicPerformance = $mechanics->map(function (User $mechanic) use ($bookings): array {
            $assigned = $bookings->where('mechanic_id', $mechanic->id);

            return [
                'mechanic' => $mechanic,
                'active' => $assigned->whereIn('status', ['assigned', 'in_progress'])->count(),
                'completed' => $assigned->where('status', 'completed')->count(),
                'revenue' => $assigned->where('status', 'completed')->sum(fn (ServiceBooking $booking): float => $booking->total),
            ];
        })->sortByDesc('completed')->values();

        $stats = [
            'revenue' => $totalRevenue,
            'bookings' => $bookings->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'active' => $bookings->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed' => $completedBookings->count(),
            'low_stock' => $parts->filter->isLowStock()->count(),
            'customers' => $customers->count(),
            'mechanics' => $mechanics->count(),
            'audit_logs' => $auditLogs->count(),
        ];

        return [
            'money' => fn ($value) => 'PHP '.number_format((float) $value, 2),
            'statusLabel' => fn ($status) => ucwords(str_replace('_', ' ', $status)),
            'bookings' => $bookings,
            'mechanics' => $mechanics,
            'customers' => $customers,
            'parts' => $parts,
            'stats' => $stats,
            'monthlyRevenue' => $monthlyRevenue,
            'serviceRevenue' => $serviceRevenue,
            'mechanicPerformance' => $mechanicPerformance,
            'maxMonthlyRevenue' => max(1, (float) $monthlyRevenue->max()),
            'maxServiceRevenue' => max(1, (float) $serviceRevenue->max()),
            'auditLogs' => $auditLogs,
        ];
    }

    public function adminProfile(Request $request): View
    {
        $admin = $request->user();

        return view('dashboards.admin-profile', [
            'admin' => $admin,
            'loginLogs' => LoginLog::where('user_id', $admin->id)->latest('logged_in_at')->limit(25)->get(),
            'auditLogs' => AuditLog::with('user')->where('user_id', $admin->id)->latest()->limit(25)->get(),
        ]);
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $validated = $this->bookingRules($request);
        $customer = $this->selectedCustomer($validated['customer_id'] ?? null);

        $booking = ServiceBooking::create([
            ...$validated,
            'service_code' => 'RS-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'customer_name' => $customer?->name ?? $validated['customer_name'],
            'customer_email' => $customer?->email ?? ($validated['customer_email'] ?? null),
            'customer_phone' => $validated['customer_phone'] ?? null,
            'status' => ($validated['mechanic_id'] ?? null) ? 'assigned' : 'pending',
            'labor_cost' => $validated['labor_cost'] ?? 0,
            'parts_cost' => $validated['parts_cost'] ?? 0,
        ]);

        $this->audit($request, 'booking.created', $booking, "Created booking {$booking->service_code}");

        return back()->with('status', 'Booking created.');
    }

    public function updateBooking(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $validated = $this->bookingRules($request, $booking);
        $customer = $this->selectedCustomer($validated['customer_id'] ?? null);

        $booking->update([
            ...$validated,
            'customer_name' => $customer?->name ?? $validated['customer_name'],
            'customer_email' => $customer?->email ?? ($validated['customer_email'] ?? null),
            'customer_phone' => $validated['customer_phone'] ?? null,
            'labor_cost' => $validated['labor_cost'] ?? 0,
            'parts_cost' => $validated['parts_cost'] ?? 0,
        ]);

        $this->audit($request, 'booking.updated', $booking, "Updated booking {$booking->service_code}");

        return back()->with('status', 'Booking updated.');
    }

    public function updateBookingStatus(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(ServiceBooking::STATUSES)],
        ]);

        $booking->update($validated);

        $this->audit($request, 'booking.status_updated', $booking, "Changed {$booking->service_code} to {$validated['status']}");

        return back()->with('status', 'Booking status updated.');
    }

    public function assignMechanic(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'mechanic_id' => ['required', Rule::exists('users', 'id')->where('role', 'mechanic')],
        ]);

        $booking->update([
            'mechanic_id' => $validated['mechanic_id'],
            'status' => $booking->status === 'pending' ? 'assigned' : $booking->status,
        ]);

        $this->audit($request, 'booking.assigned', $booking, "Assigned {$booking->service_code} to mechanic #{$validated['mechanic_id']}");

        return back()->with('status', 'Mechanic assigned.');
    }

    public function deleteBooking(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $summary = "Deleted booking {$booking->service_code}";
        $booking->delete();
        $this->audit($request, 'booking.deleted', null, $summary);

        return back()->with('status', 'Booking deleted.');
    }

    public function storePart(Request $request): RedirectResponse
    {
        $part = SparePart::create($this->partRules($request));
        $this->audit($request, 'part.created', $part, "Added spare part {$part->sku}");

        return back()->with('status', 'Spare part added.');
    }

    public function updatePart(Request $request, SparePart $part): RedirectResponse
    {
        $part->update($this->partRules($request, $part));
        $this->audit($request, 'part.updated', $part, "Updated spare part {$part->sku}");

        return back()->with('status', 'Spare part updated.');
    }

    public function deletePart(Request $request, SparePart $part): RedirectResponse
    {
        $summary = "Deleted spare part {$part->sku}";
        $part->delete();
        $this->audit($request, 'part.deleted', null, $summary);

        return back()->with('status', 'Spare part deleted.');
    }

    public function storeMechanic(Request $request): RedirectResponse
    {
        $validated = $this->userRules($request);
        $validated['role'] = 'mechanic';
        $validated['password'] = Hash::make($validated['password']);

        $mechanic = User::create($validated);
        $this->audit($request, 'mechanic.created', $mechanic, "Added mechanic {$mechanic->email}");

        return back()->with('status', 'Mechanic added.');
    }

    public function updateMechanic(Request $request, User $mechanic): RedirectResponse
    {
        abort_unless($mechanic->role === 'mechanic', 404);

        $mechanic->update($this->userUpdatePayload($request, $mechanic));
        $this->audit($request, 'mechanic.updated', $mechanic, "Updated mechanic {$mechanic->email}");

        return back()->with('status', 'Mechanic updated.');
    }

    public function deleteMechanic(Request $request, User $mechanic): RedirectResponse
    {
        abort_unless($mechanic->role === 'mechanic', 404);
        $summary = "Deleted mechanic {$mechanic->email}";
        $mechanic->delete();
        $this->audit($request, 'mechanic.deleted', null, $summary);

        return back()->with('status', 'Mechanic deleted.');
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $validated = $this->userRules($request);
        $validated['role'] = 'customer';
        $validated['password'] = Hash::make($validated['password']);

        $customer = User::create($validated);
        $this->audit($request, 'customer.created', $customer, "Added customer {$customer->email}");

        return back()->with('status', 'Customer added.');
    }

    public function updateCustomer(Request $request, User $customer): RedirectResponse
    {
        abort_unless($customer->role === 'customer', 404);

        $customer->update($this->userUpdatePayload($request, $customer));
        $this->audit($request, 'customer.updated', $customer, "Updated customer {$customer->email}");

        return back()->with('status', 'Customer updated.');
    }

    public function deleteCustomer(Request $request, User $customer): RedirectResponse
    {
        abort_unless($customer->role === 'customer', 404);
        $summary = "Deleted customer {$customer->email}";
        $customer->delete();
        $this->audit($request, 'customer.deleted', null, $summary);

        return back()->with('status', 'Customer deleted.');
    }

    public function mechanic(Request $request): View
    {
        return view('dashboards.mechanic', $this->mechanicDashboardData($request, 'dashboard'));
    }

    public function mechanicRepairs(Request $request): View
    {
        return view('dashboards.mechanic', $this->mechanicDashboardData($request, 'repairs'));
    }

    public function mechanicPerformance(Request $request): View
    {
        return view('dashboards.mechanic', $this->mechanicDashboardData($request, 'performance'));
    }

    public function updateMechanicRepair(Request $request, ServiceBooking $booking): RedirectResponse
    {
        abort_unless($booking->mechanic_id === $request->user()->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['assigned', 'in_progress', 'completed', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'spare_part_id' => ['nullable', Rule::exists('spare_parts', 'id')],
            'quantity' => ['nullable', 'required_with:spare_part_id', 'integer', 'min:1', 'max:999'],
        ]);

        DB::transaction(function () use ($booking, $request, $validated): void {
            $booking->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            if (! empty($validated['spare_part_id'])) {
                $part = SparePart::lockForUpdate()->findOrFail($validated['spare_part_id']);
                $quantity = (int) $validated['quantity'];

                if ($part->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Only {$part->quantity} {$part->name} left in inventory.",
                    ]);
                }

                $unitPrice = (float) $part->price;

                RepairPartUsage::create([
                    'service_booking_id' => $booking->id,
                    'spare_part_id' => $part->id,
                    'mechanic_id' => $request->user()->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $quantity,
                ]);

                $part->decrement('quantity', $quantity);
                $booking->update([
                    'parts_cost' => RepairPartUsage::where('service_booking_id', $booking->id)->sum('total_price'),
                ]);
            }
        });

        $this->audit($request, 'mechanic.repair_updated', $booking, "Updated repair {$booking->service_code}");

        return back()->with('status', 'Repair updated.');
    }

    public function customer(): View
    {
        return view('dashboards.customer');
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingRules(Request $request, ?ServiceBooking $booking = null): array
    {
        return $request->validate([
            'customer_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'customer')],
            'mechanic_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'mechanic')],
            'customer_name' => [$request->filled('customer_id') ? 'nullable' : 'required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'vehicle' => ['required', 'string', 'max:255'],
            'service_type' => ['required', 'string', 'max:120'],
            'status' => [$booking ? 'required' : 'nullable', Rule::in(ServiceBooking::STATUSES)],
            'scheduled_at' => ['nullable', 'date'],
            'labor_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'parts_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function partRules(Request $request, ?SparePart $part = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:80', Rule::unique('spare_parts', 'sku')->ignore($part)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:120'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'unit_cost' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'supplier' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function userRules(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->max(24)->letters()->numbers()->symbols()],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function userUpdatePayload(Request $request, User $user): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'confirmed', Password::min(8)->max(24)->letters()->numbers()->symbols()],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function mechanicDashboardData(Request $request, string $page): array
    {
        $mechanic = $request->user();
        $status = $request->query('status');
        $validStatuses = ServiceBooking::STATUSES;

        if (! in_array($status, $validStatuses, true)) {
            $status = null;
        }

        $assignedQuery = ServiceBooking::with(['customer', 'partUsages.part'])
            ->where('mechanic_id', $mechanic->id)
            ->latest('scheduled_at')
            ->latest();

        $allRepairs = (clone $assignedQuery)->get();
        $filteredRepairs = (clone $assignedQuery)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->get();

        $activeRepairs = $allRepairs->whereIn('status', ['assigned', 'in_progress']);
        $completedRepairs = $allRepairs->where('status', 'completed');
        $billableRepairs = $allRepairs->whereNotIn('status', ['cancelled']);
        $completionRate = $billableRepairs->count() > 0
            ? round(($completedRepairs->count() / $billableRepairs->count()) * 100)
            : 0;

        $earnings = $completedRepairs->sum(fn (ServiceBooking $booking): float => (float) $booking->labor_cost);
        $partsHandled = $allRepairs->sum(fn (ServiceBooking $booking): int => $booking->partUsages->sum('quantity'));
        $statusCounts = collect($validStatuses)
            ->mapWithKeys(fn (string $item): array => [$item => $allRepairs->where('status', $item)->count()]);

        return [
            'page' => $page,
            'repairs' => $filteredRepairs,
            'allRepairs' => $allRepairs,
            'activeRepairs' => $activeRepairs,
            'parts' => SparePart::orderBy('name')->get(),
            'statusFilter' => $status,
            'statusCounts' => $statusCounts,
            'statusLabel' => fn ($item) => ucwords(str_replace('_', ' ', $item)),
            'money' => fn ($value) => 'PHP '.number_format((float) $value, 2),
            'stats' => [
                'assigned' => $allRepairs->count(),
                'active' => $activeRepairs->count(),
                'completed' => $completedRepairs->count(),
                'completion_rate' => $completionRate,
                'earnings' => $earnings,
                'parts_handled' => $partsHandled,
            ],
        ];
    }

    private function selectedCustomer(mixed $customerId): ?User
    {
        if (! $customerId) {
            return null;
        }

        return User::where('role', 'customer')->find($customerId);
    }

    private function audit(Request $request, string $action, mixed $model, string $summary): void
    {
        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->id,
            'summary' => $summary,
            'properties' => [
                'route' => $request->route()?->getName(),
            ],
            'ip_address' => $request->ip(),
        ]);
    }
}
