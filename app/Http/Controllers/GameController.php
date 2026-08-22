<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\GameAlert;
use App\Models\GameSession;
use App\Services\LedgerService;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function __construct(
        protected SimulationService $simulationService,
        protected LedgerService $ledgerService
    ) {}

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company?->gameSession ?? GameSession::first();

        $income = $company ? $this->ledgerService->getIncomeStatement($company) : [];
        $balance = $company ? $this->ledgerService->getBalanceSheet($company) : [];
        $alerts = $company ? GameAlert::where('company_id', $company->id)->latest()->take(10)->get() : collect();

        $employeesCount = $company ? $company->employees()->where('status', 'active')->count() : 0;
        $productsCount = $company ? $company->products()->where('status', 'active')->count() : 0;
        $facilitiesCount = $company ? $company->facilities()->count() : 0;

        return view('dashboard', compact(
            'company',
            'session',
            'income',
            'balance',
            'alerts',
            'employeesCount',
            'productsCount',
            'facilitiesCount'
        ));
    }

    public function advanceTime(Request $request): JsonResponse
    {
        $days = (int)$request->input('days', 1);
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company?->gameSession ?? GameSession::first();

        if (!$session) {
            return response()->json(['error' => 'No active game session'], 404);
        }

        $results = $this->simulationService->advanceDays($session, min(30, max(1, $days)));

        return response()->json([
            'success' => true,
            'current_date' => $session->current_game_date->toDateString(),
            'cash_balance' => (float)$company->fresh()->cash_balance,
            'valuation' => (float)$company->fresh()->valuation,
            'results' => $results,
        ]);
    }

    public function setSpeed(Request $request): JsonResponse
    {
        $speed = $request->input('speed', '1x');
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company?->gameSession ?? GameSession::first();

        if ($session) {
            $session->speed = $speed;
            $session->is_paused = ($speed === 'paused');
            $session->save();
        }

        return response()->json(['success' => true, 'speed' => $speed]);
    }
}
