<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RdProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RDController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();

        $projects = RdProject::where('company_id', $company->id)->get();
        $activeProjects = $projects->where('status', 'in_progress');
        $completedProjects = $projects->where('status', 'completed');
        $totalRdSpend = $projects->sum('spent_amount');

        return view('views.rd', compact('company', 'projects', 'activeProjects', 'completedProjects', 'totalRdSpend'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->activeCompany() ?? Company::first();
        $session = $company->gameSession;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'daily_budget' => 'required|numeric|min:500',
            'total_budget' => 'required|numeric|min:10000',
        ]);

        $project = RdProject::create([
            'company_id' => $company->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'daily_budget' => $validated['daily_budget'],
            'total_budget' => $validated['total_budget'],
            'spent_amount' => 0,
            'progress_pct' => 0,
            'start_date' => $session->current_game_date->toDateString(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('rd.index')->with('success', "Initiated R&D project '{$project->name}'!");
    }
}
