<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Competitor;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\GameAlert;
use App\Models\GameSession;
use App\Models\Industry;
use App\Models\Investment;
use App\Models\MarketingCampaign;
use App\Models\MarketSegment;
use App\Models\Product;
use App\Models\ProductBillOfMaterial;
use App\Models\ProductionLine;
use App\Models\RawMaterial;
use App\Models\RdProject;
use App\Models\SalesChannel;
use App\Models\Shareholder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\HRService;
use App\Services\InventoryService;
use App\Services\LedgerService;
use App\Services\ProcurementService;
use App\Services\ProductionService;
use App\Services\SalesService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ledgerService = app(LedgerService::class);
        $inventoryService = app(InventoryService::class);
        $procurementService = app(ProcurementService::class);
        $productionService = app(ProductionService::class);
        $hrService = app(HRService::class);
        $salesService = app(SalesService::class);

        // 1. Countries
        $usa = Country::firstOrCreate(['code' => 'US'], ['name' => 'United States', 'currency_code' => 'USD', 'tax_rate' => 0.21, 'labor_cost_index' => 1.00, 'logistics_index' => 1.00]);
        $germany = Country::firstOrCreate(['code' => 'DE'], ['name' => 'Germany', 'currency_code' => 'EUR', 'tax_rate' => 0.25, 'labor_cost_index' => 1.10, 'logistics_index' => 1.05]);
        $japan = Country::firstOrCreate(['code' => 'JP'], ['name' => 'Japan', 'currency_code' => 'JPY', 'tax_rate' => 0.23, 'labor_cost_index' => 0.95, 'logistics_index' => 0.98]);
        $singapore = Country::firstOrCreate(['code' => 'SG'], ['name' => 'Singapore', 'currency_code' => 'SGD', 'tax_rate' => 0.17, 'labor_cost_index' => 0.90, 'logistics_index' => 1.15]);

        // 2. Industry
        $industry = Industry::firstOrCreate(['code' => 'TECH_ROBOTICS'], [
            'name' => 'High-Tech & Robotics',
            'description' => 'Advanced industrial automation, robotics, and smart edge hardware.',
            'market_size' => 4500000000.00,
            'growth_rate' => 0.145,
            'volatility' => 0.12,
        ]);

        // 3. Market Segments & Competitors
        MarketSegment::firstOrCreate(['industry_id' => $industry->id, 'name' => 'Industrial Robotics'], ['total_market_size_usd' => 2100000000.00, 'annual_growth_rate' => 0.16, 'price_sensitivity' => 45, 'quality_sensitivity' => 85]);
        MarketSegment::firstOrCreate(['industry_id' => $industry->id, 'name' => 'AI Sensors & Edge IoT'], ['total_market_size_usd' => 1400000000.00, 'annual_growth_rate' => 0.22, 'price_sensitivity' => 60, 'quality_sensitivity' => 75]);
        MarketSegment::firstOrCreate(['industry_id' => $industry->id, 'name' => 'Commercial Drones'], ['total_market_size_usd' => 1000000000.00, 'annual_growth_rate' => 0.11, 'price_sensitivity' => 70, 'quality_sensitivity' => 65]);

        Competitor::firstOrCreate(['industry_id' => $industry->id, 'name' => 'Quantum Corp'], ['market_share_pct' => 18.10, 'brand_rating' => 88, 'average_price_index' => 1.15]);
        Competitor::firstOrCreate(['industry_id' => $industry->id, 'name' => 'Apex Systems'], ['market_share_pct' => 15.20, 'brand_rating' => 82, 'average_price_index' => 0.95]);
        Competitor::firstOrCreate(['industry_id' => $industry->id, 'name' => 'Zenith Dynamics'], ['market_share_pct' => 12.80, 'brand_rating' => 79, 'average_price_index' => 0.90]);

        // 4. Raw Materials
        $mcu = RawMaterial::firstOrCreate(['sku' => 'MAT-MCU-01'], ['name' => 'Quantum ARM Microcontroller', 'category' => 'Semiconductors', 'unit' => 'pcs', 'base_cost' => 120.00, 'quality_score' => 92]);
        $titanium = RawMaterial::firstOrCreate(['sku' => 'MAT-TITAN-02'], ['name' => 'Aero-grade Titanium Plate', 'category' => 'Metals', 'unit' => 'kg', 'base_cost' => 85.00, 'quality_score' => 95]);
        $optics = RawMaterial::firstOrCreate(['sku' => 'MAT-OPTIC-03'], ['name' => 'LiDAR Optical Sensor Module', 'category' => 'Sensors', 'unit' => 'pcs', 'base_cost' => 240.00, 'quality_score' => 90]);
        $battery = RawMaterial::firstOrCreate(['sku' => 'MAT-BATT-04'], ['name' => 'High-Density Lithium Pack (5kWh)', 'category' => 'Energy', 'unit' => 'unit', 'base_cost' => 450.00, 'quality_score' => 88]);
        $actuator = RawMaterial::firstOrCreate(['sku' => 'MAT-ACT-05'], ['name' => 'Precision Servo Actuator', 'category' => 'Mechanical', 'unit' => 'pcs', 'base_cost' => 175.00, 'quality_score' => 94]);

        // 5. Suppliers
        $sup1 = Supplier::firstOrCreate(['name' => 'Silicon Valley Microfab'], ['country_id' => $usa->id, 'contact_email' => 'sales@sv-microfab.com', 'lead_time_days' => 2, 'quality_rating' => 95, 'reliability_rating' => 94, 'price_multiplier' => 1.05]);
        $sup2 = Supplier::firstOrCreate(['name' => 'Global Titanium & Alloys'], ['country_id' => $germany->id, 'contact_email' => 'orders@titan-alloys.de', 'lead_time_days' => 4, 'quality_rating' => 96, 'reliability_rating' => 92, 'price_multiplier' => 1.00]);
        $sup3 = Supplier::firstOrCreate(['name' => 'Tokyo Precision Optics'], ['country_id' => $japan->id, 'contact_email' => 'contact@tokyo-optics.jp', 'lead_time_days' => 3, 'quality_rating' => 98, 'reliability_rating' => 96, 'price_multiplier' => 1.10]);
        $sup4 = Supplier::firstOrCreate(['name' => 'SingaPower Energy Solutions'], ['country_id' => $singapore->id, 'contact_email' => 'sales@singapower.sg', 'lead_time_days' => 2, 'quality_rating' => 90, 'reliability_rating' => 95, 'price_multiplier' => 0.95]);

        $sup1->rawMaterials()->syncWithoutDetaching([$mcu->id => ['unit_price' => 125.00, 'min_order_quantity' => 100]]);
        $sup2->rawMaterials()->syncWithoutDetaching([$titanium->id => ['unit_price' => 85.00, 'min_order_quantity' => 50]]);
        $sup3->rawMaterials()->syncWithoutDetaching([$optics->id => ['unit_price' => 250.00, 'min_order_quantity' => 50], $actuator->id => ['unit_price' => 180.00, 'min_order_quantity' => 80]]);
        $sup4->rawMaterials()->syncWithoutDetaching([$battery->id => ['unit_price' => 430.00, 'min_order_quantity' => 25]]);

        // 6. User (CEO)
        $user = User::firstOrCreate(['email' => 'ceo@novatech.corp'], [
            'name' => 'Alex Vance (CEO)',
            'password' => Hash::make('password'),
            'status' => 'active',
            'last_login_at' => now(),
        ]);

        // 7. Game Session
        $session = GameSession::firstOrCreate(['user_id' => $user->id], [
            'current_game_date' => '2026-08-19',
            'speed' => '1x',
            'is_paused' => false,
            'difficulty' => 'standard',
            'scenario' => 'high_growth_tech',
        ]);

        // 8. Company
        $company = Company::firstOrCreate(['user_id' => $user->id, 'ticker' => 'NOVA'], [
            'game_session_id' => $session->id,
            'industry_id' => $industry->id,
            'country_id' => $usa->id,
            'name' => 'NovaTech Industries',
            'cash_balance' => 42800000.00,
            'credit_rating' => 'AAA',
            'share_price' => 28.40,
            'total_shares' => 10000000,
            'valuation' => 284000000.00,
            'market_share_pct' => 23.40,
            'reputation_score' => 92,
            'founded_date' => '2024-01-01',
        ]);

        // Setup Chart of Accounts & Starting Capital
        $ledgerService->setupCompanyAccounts($company, 42800000.00);

        // 9. Facilities & Warehouse
        $factory = Facility::firstOrCreate(['company_id' => $company->id, 'name' => 'Factory A - Advanced Robotics Plant'], [
            'country_id' => $usa->id,
            'type' => 'factory',
            'status' => 'operational',
            'building_condition' => 96,
            'efficiency_rating' => 91,
            'max_capacity' => 2500,
            'purchase_cost' => 35000000.00,
            'monthly_operating_cost' => 120000.00,
        ]);

        $hq = Facility::firstOrCreate(['company_id' => $company->id, 'name' => 'NovaTech Corporate HQ & Lab'], [
            'country_id' => $usa->id,
            'type' => 'office',
            'status' => 'operational',
            'building_condition' => 98,
            'efficiency_rating' => 95,
            'max_capacity' => 1000,
            'purchase_cost' => 20000000.00,
            'monthly_operating_cost' => 65000.00,
        ]);

        $warehouse = Warehouse::firstOrCreate(['company_id' => $company->id, 'name' => 'Central Distribution Hub #1'], [
            'location' => 'Austin, TX',
            'max_capacity_units' => 150000,
            'current_capacity_units' => 12400,
            'monthly_lease_cost' => 15000.00,
        ]);

        // 10. Departments
        $deptExec = Department::firstOrCreate(['company_id' => $company->id, 'code' => 'EXEC'], ['name' => 'Executive Office', 'budget' => 500000.00, 'headcount' => 5, 'efficiency' => 95]);
        $deptRD = Department::firstOrCreate(['company_id' => $company->id, 'code' => 'R&D'], ['name' => 'Research & Engineering', 'budget' => 2500000.00, 'headcount' => 320, 'efficiency' => 94]);
        $deptOps = Department::firstOrCreate(['company_id' => $company->id, 'code' => 'OPS'], ['name' => 'Manufacturing & Operations', 'budget' => 3800000.00, 'headcount' => 380, 'efficiency' => 91]);
        $deptSales = Department::firstOrCreate(['company_id' => $company->id, 'code' => 'SALES'], ['name' => 'Sales & Business Development', 'budget' => 1200000.00, 'headcount' => 95, 'efficiency' => 89]);
        $deptFin = Department::firstOrCreate(['company_id' => $company->id, 'code' => 'FIN'], ['name' => 'Finance & Accounting', 'budget' => 450000.00, 'headcount' => 47, 'efficiency' => 96]);

        // 11. Initial Key Employees
        $employeesData = [
            ['first_name' => 'Marcus', 'last_name' => 'Sterling', 'role' => 'VP of Engineering', 'salary' => 18500, 'dept' => $deptRD],
            ['first_name' => 'Elena', 'last_name' => 'Rostova', 'role' => 'Chief Robotics Architect', 'salary' => 21000, 'dept' => $deptRD],
            ['first_name' => 'David', 'last_name' => 'Chen', 'role' => 'Director of Manufacturing', 'salary' => 16000, 'dept' => $deptOps],
            ['first_name' => 'Sarah', 'last_name' => 'Jenkins', 'role' => 'VP of Global Sales', 'salary' => 17500, 'dept' => $deptSales],
            ['first_name' => 'Robert', 'last_name' => 'Kim', 'role' => 'Corporate Controller', 'salary' => 14000, 'dept' => $deptFin],
            ['first_name' => 'Jack', 'last_name' => 'Harrison', 'role' => 'Senior Automation Engineer', 'salary' => 12000, 'dept' => $deptRD],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'role' => 'Production Supervisor', 'salary' => 9500, 'dept' => $deptOps],
            ['first_name' => 'Liam', 'last_name' => 'Nakamura', 'role' => 'Enterprise Account Executive', 'salary' => 11000, 'dept' => $deptSales],
        ];

        foreach ($employeesData as $emp) {
            Employee::firstOrCreate(
                ['company_id' => $company->id, 'first_name' => $emp['first_name'], 'last_name' => $emp['last_name']],
                [
                    'department_id' => $emp['dept']->id,
                    'facility_id' => $factory->id,
                    'role_title' => $emp['role'],
                    'salary' => $emp['salary'],
                    'skill_level' => rand(85, 98),
                    'morale' => rand(85, 95),
                    'productivity' => rand(88, 96),
                    'hired_date' => '2024-02-15',
                    'status' => 'active',
                ]
            );
        }

        // 12. Products & BOM
        $prod1 = Product::firstOrCreate(['company_id' => $company->id, 'sku' => 'PROD-ROBO-X1'], [
            'name' => 'NovaBot Autonomous Industrial Unit',
            'category' => 'Robotics',
            'description' => 'Heavy-duty 6-axis AI collaborative robot with LiDAR positioning.',
            'selling_price' => 14500.00,
            'target_unit_cost' => 5200.00,
            'quality_rating' => 94,
            'appeal_score' => 90,
            'status' => 'active',
        ]);

        $prod2 = Product::firstOrCreate(['company_id' => $company->id, 'sku' => 'PROD-SENS-Q2'], [
            'name' => 'QuantumVision Spatial Sensor Array',
            'category' => 'Sensors',
            'description' => 'Ultra-high-rate 3D spatial mapping and defect detection sensor.',
            'selling_price' => 3800.00,
            'target_unit_cost' => 1400.00,
            'quality_rating' => 96,
            'appeal_score' => 92,
            'status' => 'active',
        ]);

        // BOM Wiring
        $prod1->rawMaterials()->syncWithoutDetaching([
            $mcu->id => ['quantity_required' => 4],
            $titanium->id => ['quantity_required' => 25],
            $optics->id => ['quantity_required' => 2],
            $battery->id => ['quantity_required' => 2],
            $actuator->id => ['quantity_required' => 6],
        ]);

        $prod2->rawMaterials()->syncWithoutDetaching([
            $mcu->id => ['quantity_required' => 2],
            $optics->id => ['quantity_required' => 3],
            $titanium->id => ['quantity_required' => 4],
        ]);

        // 13. Production Lines
        $line1 = ProductionLine::firstOrCreate(['facility_id' => $factory->id, 'name' => 'Robotics Automated Assembly Line 1'], [
            'product_id' => $prod1->id,
            'daily_capacity_units' => 25,
            'current_utilization_pct' => 91.00,
            'efficiency_rating' => 94,
            'status' => 'running',
        ]);

        $line2 = ProductionLine::firstOrCreate(['facility_id' => $factory->id, 'name' => 'Sensor High-Speed SMD Line 2'], [
            'product_id' => $prod2->id,
            'daily_capacity_units' => 80,
            'current_utilization_pct' => 84.00,
            'efficiency_rating' => 96,
            'status' => 'running',
        ]);

        // 14. Stock Initial Inventory
        $inventoryService->addStock($company, $warehouse, 'raw_material', $mcu->id, 2500, 125.00, 'adjustment', '2026-08-01');
        $inventoryService->addStock($company, $warehouse, 'raw_material', $titanium->id, 8000, 85.00, 'adjustment', '2026-08-01');
        $inventoryService->addStock($company, $warehouse, 'raw_material', $optics->id, 1200, 250.00, 'adjustment', '2026-08-01');
        $inventoryService->addStock($company, $warehouse, 'raw_material', $battery->id, 850, 430.00, 'adjustment', '2026-08-01');
        $inventoryService->addStock($company, $warehouse, 'raw_material', $actuator->id, 3200, 180.00, 'adjustment', '2026-08-01');

        $inventoryService->addStock($company, $warehouse, 'finished_good', $prod1->id, 240, 5200.00, 'production_in', '2026-08-10');
        $inventoryService->addStock($company, $warehouse, 'finished_good', $prod2->id, 650, 1400.00, 'production_in', '2026-08-10');

        // 15. Sales Channels & Customers
        $channelDirect = SalesChannel::firstOrCreate(['company_id' => $company->id, 'name' => 'Enterprise Direct Sales Team'], ['type' => 'direct', 'commission_rate' => 0.0400, 'monthly_fixed_cost' => 85000.00, 'is_active' => true]);
        $channelOnline = SalesChannel::firstOrCreate(['company_id' => $company->id, 'name' => 'NovaTech B2B Portal'], ['type' => 'ecommerce', 'commission_rate' => 0.0150, 'monthly_fixed_cost' => 12000.00, 'is_active' => true]);

        $cust1 = Customer::firstOrCreate(['name' => 'Tesla Gigafactory Texas'], ['segment' => 'enterprise', 'credit_rating' => 'AAA', 'loyalty_score' => 92]);
        $cust2 = Customer::firstOrCreate(['name' => 'Amazon Robotics Fulfillment'], ['segment' => 'enterprise', 'credit_rating' => 'AAA', 'loyalty_score' => 95]);
        $cust3 = Customer::firstOrCreate(['name' => 'Siemens Industrial Automation'], ['segment' => 'b2b', 'credit_rating' => 'AA', 'loyalty_score' => 88]);
        $cust4 = Customer::firstOrCreate(['name' => 'Boeing Defense & Space'], ['segment' => 'government', 'credit_rating' => 'AAA', 'loyalty_score' => 90]);

        // 16. Generate Initial Revenue / Sales Transactions in Ledger
        $salesService->processSalesOrder($company, $cust1, $channelDirect, $warehouse, $prod1, 40, 14500.00, '2026-08-12');
        $salesService->processSalesOrder($company, $cust2, $channelDirect, $warehouse, $prod1, 65, 14200.00, '2026-08-15');
        $salesService->processSalesOrder($company, $cust3, $channelOnline, $warehouse, $prod2, 120, 3800.00, '2026-08-17');
        $salesService->processSalesOrder($company, $cust4, $channelDirect, $warehouse, $prod2, 200, 3750.00, '2026-08-18');

        // 17. Marketing Campaign & R&D Project
        MarketingCampaign::firstOrCreate(['company_id' => $company->id, 'name' => 'Global Automation Summit Campaign'], [
            'product_id' => $prod1->id,
            'channel' => 'digital',
            'daily_budget' => 3500.00,
            'total_spend' => 45500.00,
            'start_date' => '2026-08-01',
            'reach' => 145000,
            'leads_generated' => 850,
            'status' => 'active',
        ]);

        RdProject::firstOrCreate(['company_id' => $company->id, 'name' => 'Next-Gen Autonomous Navigation Core v4'], [
            'category' => 'AI Algorithms',
            'daily_budget' => 6500.00,
            'total_budget' => 250000.00,
            'spent_amount' => 145000.00,
            'progress_pct' => 58.00,
            'start_date' => '2026-07-01',
            'estimated_completion_date' => '2026-10-15',
            'status' => 'in_progress',
        ]);

        // 18. Shareholders
        Shareholder::firstOrCreate(['company_id' => $company->id, 'name' => 'Alex Vance (Founder)'], [
            'type' => 'founder',
            'share_count' => 6500000,
            'ownership_percentage' => 65.00,
            'total_invested' => 5000000.00,
            'voting_power_pct' => 65.00,
        ]);
        Shareholder::firstOrCreate(['company_id' => $company->id, 'name' => 'Sequoia Horizon Ventures'], [
            'type' => 'vc',
            'share_count' => 2500000,
            'ownership_percentage' => 25.00,
            'total_invested' => 25000000.00,
            'voting_power_pct' => 25.00,
        ]);
        Shareholder::firstOrCreate(['company_id' => $company->id, 'name' => 'Employee Incentive Stock Pool'], [
            'type' => 'employee',
            'share_count' => 1000000,
            'ownership_percentage' => 10.00,
            'total_invested' => 1000000.00,
            'voting_power_pct' => 10.00,
        ]);

        // 19. Investments
        Investment::firstOrCreate(['company_id' => $company->id, 'name' => 'US Treasury 6-Month Yield Note'], [
            'type' => 'government_bond',
            'principal_amount' => 10000000.00,
            'current_value' => 10240000.00,
            'annual_return_rate' => 0.0525,
            'purchase_date' => '2026-03-01',
            'maturity_date' => '2026-09-01',
            'status' => 'active',
        ]);

        // 20. Alerts
        GameAlert::firstOrCreate(['company_id' => $company->id, 'title' => 'Strong Q3 Revenue Inflow'], [
            'game_session_id' => $session->id,
            'type' => 'success',
            'message' => 'NovaTech recorded strong revenue exceeding monthly targets by 14.2%.',
            'category' => 'finance',
            'is_read' => false,
            'game_date' => '2026-08-19',
        ]);

        GameAlert::firstOrCreate(['company_id' => $company->id, 'title' => 'Factory A Utilization High (91%)'], [
            'game_session_id' => $session->id,
            'type' => 'warning',
            'message' => 'Production line is operating near peak capacity. Consider commissioning Line 3 or expanding facility.',
            'category' => 'operations',
            'is_read' => false,
            'game_date' => '2026-08-18',
        ]);
    }
}
