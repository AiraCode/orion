<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\GameSession;
use App\Services\SimulationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationTickTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulation_advance_days(): void
    {
        $this->seed();
        $simService = app(SimulationService::class);
        $session = GameSession::first();

        $initialDate = $session->current_game_date->toDateString();

        $results = $simService->advanceDays($session, 3);

        $this->assertCount(3, $results);
        $this->assertNotEquals($initialDate, $session->fresh()->current_game_date->toDateString());
    }
}
