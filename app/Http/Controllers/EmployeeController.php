<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\PayrollRecord;
use App\Services\HRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct(protected HRService $hrService) {}

    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $employees = Employee::where('company_id', $company->id)->with(['department', 'facility'])->get();
        $departments = Department::where('company_id', $company->id)->withCount('employees')->get();
        $facilities = Facility::where('company_id', $company->id)->get();
        $payrolls = PayrollRecord::where('company_id', $company->id)->latest('pay_date')->take(12)->get();

        $totalSalaries = $employees->sum('salary');
        $avgMorale = $employees->avg('morale') ?? 0;
        $avgProductivity = $employees->avg('productivity') ?? 0;

        return view('views.employees', compact(
            'company',
            'employees',
            'departments',
            'facilities',
            'payrolls',
            'totalSalaries',
            'avgMorale',
            'avgProductivity'
        ));
    }

    public function hire(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company->gameSession;

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'role_title' => 'required|string|max:150',
            'department_id' => 'required|exists:departments,id',
            'salary' => 'required|numeric|min:1000',
        ]);

        $dept = Department::findOrFail($validated['department_id']);
        $facility = Facility::where('company_id', $company->id)->first();

        $this->hrService->hireEmployee(
            company: $company,
            department: $dept,
            facility: $facility,
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            roleTitle: $validated['role_title'],
            salary: (float)$validated['salary'],
            hiredDate: $session->current_game_date->toDateString()
        );

        return redirect()->route('employees.index')->with('success', "Hired {$validated['first_name']} {$validated['last_name']} as {$validated['role_title']}!");
    }
}
