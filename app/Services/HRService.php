<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\PayrollRecord;
use Illuminate\Support\Facades\DB;
use Exception;

class HRService
{
    public function __construct(protected LedgerService $ledgerService)
    {
    }

    /**
     * Hire a new employee
     */
    public function hireEmployee(
        Company $company,
        ?Department $department,
        ?Facility $facility,
        string $firstName,
        string $lastName,
        string $roleTitle,
        float $salary,
        string $hiredDate
    ): Employee {
        return DB::transaction(function () use ($company, $department, $facility, $firstName, $lastName, $roleTitle, $salary, $hiredDate) {
            $employee = Employee::create([
                'company_id' => $company->id,
                'department_id' => $department?->id,
                'facility_id' => $facility?->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'role_title' => $roleTitle,
                'salary' => $salary,
                'skill_level' => rand(75, 95),
                'morale' => rand(80, 95),
                'productivity' => rand(80, 95),
                'hired_date' => $hiredDate,
                'status' => 'active',
            ]);

            if ($department) {
                $department->headcount = Employee::where('department_id', $department->id)->where('status', 'active')->count();
                $department->save();
            }

            return $employee;
        });
    }

    /**
     * Run monthly payroll processing
     */
    public function processPayroll(Company $company, string $payDate): ?PayrollRecord
    {
        return DB::transaction(function () use ($company, $payDate) {
            $activeEmployees = Employee::where('company_id', $company->id)->where('status', 'active')->get();
            if ($activeEmployees->isEmpty()) {
                return null;
            }

            $totalGross = (float)$activeEmployees->sum('salary');
            $taxRate = 0.15; // 15% income tax withholding
            $totalTax = round($totalGross * $taxRate, 4);
            $totalNet = $totalGross - $totalTax;

            if ($company->cash_balance < $totalNet) {
                throw new Exception("Insolvent! Insufficient cash to disburse payroll. Cash: \${$company->cash_balance}, Net Payroll: \${$totalNet}");
            }

            $payroll = PayrollRecord::create([
                'company_id' => $company->id,
                'pay_date' => $payDate,
                'total_gross' => $totalGross,
                'total_tax' => $totalTax,
                'total_net' => $totalNet,
                'employee_count' => $activeEmployees->count(),
                'status' => 'processed',
            ]);

            // Double Entry Ledger:
            // Debit: Salaries & Wages Expense (6010) [Total Gross]
            // Credit: Cash (1010) [Total Net paid out]
            // Credit: Accrued Payroll & Taxes (2020) [Total Tax withheld]
            $salaryExpAcc = $this->ledgerService->getAccount($company, '6010');
            $cashAcc = $this->ledgerService->getAccount($company, '1010');
            $taxLiabAcc = $this->ledgerService->getAccount($company, '2020');

            $this->ledgerService->postTransaction(
                company: $company,
                date: $payDate,
                description: "Monthly Payroll Disbursement ({$activeEmployees->count()} employees)",
                lines: [
                    ['account_id' => $salaryExpAcc->id, 'type' => 'debit', 'amount' => $totalGross, 'memo' => 'Gross Payroll Expense'],
                    ['account_id' => $cashAcc->id, 'type' => 'credit', 'amount' => $totalNet, 'memo' => 'Net Salaries Paid'],
                    ['account_id' => $taxLiabAcc->id, 'type' => 'credit', 'amount' => $totalTax, 'memo' => 'Payroll Taxes Withheld'],
                ],
                refType: 'payroll_record',
                refId: $payroll->id
            );

            return $payroll;
        });
    }
}
