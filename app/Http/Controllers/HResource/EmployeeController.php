<?php

// App\Http\Controllers\HResource\EmployeeController.php

namespace App\Http\Controllers\HResource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Services\EmployeeService;
use App\Models\User;
use App\Models\Employee\ScheduleTemplate;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    public function index(Request $request)
    {
        // All filter values come from GET params — bookmarkable, browser-native
        $filters = $request->only([
            'search', 'department', 'employmentStatus', 'branch', 'isActive',
        ]);

        $employees = User::with(['currentSchedule.template'])
            ->filter($filters)
            ->orderBy('fullName')
            ->paginate(25)
            ->withQueryString(); // keeps filters across pages

        // For the filter dropdowns — distinct values from existing data
        $departments = User::distinct()->orderBy('department')
            ->pluck('department')->filter();
        $branches    = User::distinct()->orderBy('branch')
            ->pluck('branch')->filter();

        // Stats — run against unfiltered set
        $stats = [
            'total'        => User::count(),
            'active'       => User::where('isActive', true)->count(),
            'regular'      => User::where('employmentStatus', 'regular')->count(),
            'probationary' => User::where('employmentStatus', 'probationary')->count(),
        ];

        return view('hresource.employees.index', compact(
            'employees', 'filters', 'departments', 'branches', 'stats'
        ));
    }

    public function create()
    {
        $scheduleTemplates = ScheduleTemplate::with('days')->active()->get();
        return view('hresource.employees.create', compact('scheduleTemplates'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employee = $this->employeeService->create($request->validated());

        if ($request->filled('template_id')) {
            $this->employeeService->assignSchedule(
                $employee,
                $request->template_id,
                $request->effective_date ?? today()->toDateString()
            );
        }

        return redirect()
            ->route('hresource.employees.show', $employee)
            ->with('success', "{$employee->fullName} created successfully.");
    }

    public function show(User $employee)
    {
        $employee->load([
            'currentSchedule.template.days',
            'leaveBalances.leaveType',
            'loans' => fn($q) => $q->where('status', 'active'),
        ]);

        return view('hresource.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        $scheduleTemplates = ScheduleTemplate::with('days')->active()->get();
        $employee->load('currentSchedule.template.days');

        return view('hresource.employees.edit', compact('employee', 'scheduleTemplates'));
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        $this->employeeService->update($employee, $request->validated());

        if ($request->filled('template_id')) {
            $this->employeeService->assignSchedule(
                $employee,
                $request->template_id,
                $request->effective_date ?? today()->toDateString()
            );
        }

        return redirect()
            ->route('hresource.employees.show', $employee)
            ->with('success', "{$employee->fullName} updated successfully.");
    }

    public function toggleStatus(User $employee)
    {
        $updated = $this->employeeService->toggleStatus($employee);
        $label   = $updated->isActive ? 'activated' : 'deactivated';

        return redirect()
            ->route('hresource.employees.show', $employee)
            ->with('success', "{$employee->fullName} {$label}.");
    }
}