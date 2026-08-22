<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebViewsAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_modules_render_successfully_for_authenticated_user(): void
    {
        $this->seed();
        $user = User::where('email', 'ceo@novatech.corp')->first();

        $routes = [
            'dashboard',
            'finance.index',
            'products.index',
            'procurement.index',
            'production.index',
            'inventory.index',
            'employees.index',
            'market.index',
            'rd.index',
            'investors.index',
            'notifications.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_sidebar_highlights_active_page_correctly(): void
    {
        $this->seed();
        $user = User::where('email', 'ceo@novatech.corp')->first();

        // When visiting products
        $response = $this->actingAs($user)->get(route('products.index'));
        $response->assertStatus(200);
        $content = $response->getContent();

        // Products link should have active class
        $this->assertMatchesRegularExpression('/href="[^"]*\/products"[^>]*class="[^"]*sidebar-item[^"]*active[^"]*"/', $content);
        // Dashboard link should NOT have active class
        $this->assertMatchesRegularExpression('/href="[^"]*\/dashboard"[^>]*class="[^"]*sidebar-item[^"]*border-transparent[^"]*"/', $content);

        // When visiting dashboard
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertMatchesRegularExpression('/href="[^"]*\/dashboard"[^>]*class="[^"]*sidebar-item[^"]*active[^"]*"/', $content);
        $this->assertMatchesRegularExpression('/href="[^"]*\/products"[^>]*class="[^"]*sidebar-item[^"]*border-transparent[^"]*"/', $content);
    }
}
