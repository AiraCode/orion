<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\FinancialSnapshot;
use App\Models\GameAlert;
use App\Models\GameSession;
use App\Models\InventoryItem;
use App\Models\MarketingCampaign;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\RdProject;
use App\Models\SalesChannel;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimulationService
{
    public function __construct(
        protected LedgerService $ledgerService,
        protected InventoryService $inventoryService,
        protected ProcurementService $procurementService,
        protected ProductionService $productionService,
        protected HRService $hrService,
        protected SalesService $salesService
    ) {}

    /**
     * Advance the game simulation by a number of days
     */
    public function advanceDays(GameSession $session, int $days = 1): array
    {
        $results = [];
        for ($i = 0; $i < $days; $i++) {
            $results[] = $this->tickSingleDay($session);
        }
        return $results;
    }

    /**
     * Process a single day simulation tick
     */
    public function tickSingleDay(GameSession $session): array
    {
        return DB::transaction(function () use ($session) {
            $currentDate = Carbon::parse($session->current_game_date);
            $nextDate = $currentDate->copy()->addDay();
            $dateStr = $nextDate->toDateString();

            $company = $session->activeCompany();
            if (!$company) {
                $session->current_game_date = $dateStr;
                $session->save();
                return ['date' => $dateStr, 'status' => 'no_company'];
            }

            // 1. Process pending Purchase Orders reaching delivery date
            $pendingPOs = PurchaseOrder::where('company_id', $company->id)
                ->where('status', 'ordered')
                ->whereDate('expected_delivery_date', '<=', $dateStr)
                ->get();

            $poCount = 0;
            foreach ($pendingPOs as $po) {
                $this->procurementService->fulfillPurchaseOrder($po, $dateStr);
                $poCount++;
                GameAlert::create([
                    'company_id' => $company->id,
                    'game_session_id' => $session->id,
                    'type' => 'success',
                    'title' => 'Raw Materials Delivered',
                    'message' => "Purchase Order #{$po->id} from {$po->supplier->name} has arrived at warehouse.",
                    'category' => 'operations',
                    'game_date' => $dateStr,
                ]);
            }

            // 2. Generate daily market demand and organic sales
            $salesOrdersGenerated = 0;
            $totalSalesVolume = 0;
            $products = Product::where('company_id', $company->id)->where('status', 'active')->get();
            $defaultWarehouse = Warehouse::where('company_id', $company->id)->first();
            $defaultChannel = SalesChannel::where('company_id', $company->id)->first();
            $customers = Customer::all();

            if ($defaultWarehouse && $defaultChannel && $customers->isNotEmpty()) {
                foreach ($products as $product) {
                    $invItem = InventoryItem::where('warehouse_id', $defaultWarehouse->id)
                        ->where('item_type', 'finished_good')
                        ->where('item_id', $product->id)
                        ->first();

                    if ($invItem && $invItem->quantity > 0) {
                        // Base demand based on quality and appeal
                        $dailyDemand = rand(5, 25);
                        $sellQty = min($invItem->quantity, $dailyDemand);

                        if ($sellQty > 0) {
                            $customer = $customers->random();
                            $this->salesService->processSalesOrder(
                                company: $company,
                                customer: $customer,
                                channel: $defaultChannel,
                                warehouse: $defaultWarehouse,
                                product: $product,
                                quantity: $sellQty,
                                unitPrice: (float)$product->selling_price,
                                orderDate: $dateStr
                            );
                            $salesOrdersGenerated++;
                            $totalSalesVolume += $sellQty;
                        }
                    }
                }
            }

            // 3. Process active Marketing Campaigns daily spend
            $campaigns = MarketingCampaign::where('company_id', $company->id)->where('status', 'active')->get();
            foreach ($campaigns as $camp) {
                $dailySpend = (float)$camp->daily_budget;
                if ($company->cash_balance >= $dailySpend && $dailySpend > 0) {
                    $camp->total_spend += $dailySpend;
                    $camp->reach += rand(100, 500);
                    $camp->leads_generated += rand(2, 10);
                    $camp->save();

                    $mktAcc = $this->ledgerService->getAccount($company, '6020');
                    $cashAcc = $this->ledgerService->getAccount($company, '1010');

                    $this->ledgerService->postTransaction(
                        company: $company,
                        date: $dateStr,
                        description: "Daily Marketing Spend - {$camp->name}",
                        lines: [
                            ['account_id' => $mktAcc->id, 'type' => 'debit', 'amount' => $dailySpend, 'memo' => "Campaign #{$camp->id}"],
                            ['account_id' => $cashAcc->id, 'type' => 'credit', 'amount' => $dailySpend, 'memo' => "Marketing expense"],
                        ],
                        refType: 'marketing_campaign',
                        refId: $camp->id
                    );
                }
            }

            // 4. Progress R&D Projects
            $rdProjects = RdProject::where('company_id', $company->id)->where('status', 'in_progress')->get();
            foreach ($rdProjects as $rd) {
                $dailyBudget = (float)$rd->daily_budget;
                if ($company->cash_balance >= $dailyBudget && $dailyBudget > 0) {
                    $rd->spent_amount += $dailyBudget;
                    $progressIncrement = ($dailyBudget / max(1, (float)$rd->total_budget)) * 100;
                    $rd->progress_pct = min(100.00, $rd->progress_pct + $progressIncrement);

                    if ($rd->progress_pct >= 100.00) {
                        $rd->status = 'completed';
                        GameAlert::create([
                            'company_id' => $company->id,
                            'game_session_id' => $session->id,
                            'type' => 'success',
                            'title' => 'R&D Project Completed',
                            'message' => "Research project '{$rd->name}' has successfully completed!",
                            'category' => 'operations',
                            'game_date' => $dateStr,
                        ]);
                    }
                    $rd->save();

                    $rdAcc = $this->ledgerService->getAccount($company, '6030');
                    $cashAcc = $this->ledgerService->getAccount($company, '1010');

                    $this->ledgerService->postTransaction(
                        company: $company,
                        date: $dateStr,
                        description: "Daily R&D Spend - {$rd->name}",
                        lines: [
                            ['account_id' => $rdAcc->id, 'type' => 'debit', 'amount' => $dailyBudget, 'memo' => "R&D #{$rd->id}"],
                            ['account_id' => $cashAcc->id, 'type' => 'credit', 'amount' => $dailyBudget, 'memo' => "R&D expense"],
                        ],
                        refType: 'rd_project',
                        refId: $rd->id
                    );
                }
            }

            // 5. Monthly cycle triggers (if 1st of month)
            $isMonthly = $nextDate->day === 1;
            if ($isMonthly) {
                // Monthly Payroll
                $this->hrService->processPayroll($company, $dateStr);

                // Monthly Facility Rent
                $facilities = $company->facilities;
                foreach ($facilities as $fac) {
                    $rentCost = (float)$fac->monthly_operating_cost;
                    if ($rentCost > 0 && $company->cash_balance >= $rentCost) {
                        $rentAcc = $this->ledgerService->getAccount($company, '6040');
                        $cashAcc = $this->ledgerService->getAccount($company, '1010');

                        $this->ledgerService->postTransaction(
                            company: $company,
                            date: $dateStr,
                            description: "Monthly Facility Lease - {$fac->name}",
                            lines: [
                                ['account_id' => $rentAcc->id, 'type' => 'debit', 'amount' => $rentCost, 'memo' => "Facility {$fac->name}"],
                                ['account_id' => $cashAcc->id, 'type' => 'credit', 'amount' => $rentCost, 'memo' => "Rent & maintenance"],
                            ],
                            refType: 'facility_rent',
                            refId: $fac->id
                        );
                    }
                }
            }

            // 6. Record daily/monthly financial snapshot
            $income = $this->ledgerService->getIncomeStatement($company);
            $balance = $this->ledgerService->getBalanceSheet($company);

            // Dynamic valuation estimate (10x EBITDA or 5x Revenue + Net Assets)
            $valuation = max(1000000.0, ($income['revenue'] * 4) + $balance['total_assets'] - $balance['total_liabilities']);
            $company->valuation = $valuation;
            $company->save();

            FinancialSnapshot::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'game_date' => $dateStr,
                    'period_type' => $isMonthly ? 'monthly' : 'daily',
                ],
                [
                    'revenue' => $income['revenue'],
                    'cogs' => $income['cogs'],
                    'gross_profit' => $income['gross_profit'],
                    'operating_expenses' => $income['operating_expenses'],
                    'net_profit' => $income['net_profit'],
                    'cash_balance' => $company->cash_balance,
                    'total_assets' => $balance['total_assets'],
                    'total_liabilities' => $balance['total_liabilities'],
                    'total_equity' => $balance['total_equity'],
                    'valuation' => $valuation,
                ]
            );

            // Update session date
            $session->current_game_date = $dateStr;
            $session->save();

            return [
                'date' => $dateStr,
                'po_delivered' => $poCount,
                'sales_orders' => $salesOrdersGenerated,
                'sales_volume' => $totalSalesVolume,
                'cash_balance' => $company->cash_balance,
                'is_monthly' => $isMonthly,
            ];
        });
    }
}
