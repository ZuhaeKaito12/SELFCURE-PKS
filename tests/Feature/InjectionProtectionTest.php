<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InjectionProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_sql_injection_in_the_admin_search_is_treated_as_text(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'sme_owner', 'business_name' => 'Alpha Trading']);
        User::factory()->create(['role' => 'sme_owner', 'business_name' => 'Beta Bakery']);

        $response = $this->actingAs($admin)->get('/admin/dashboard?search='.urlencode("' OR '1'='1"));

        // A normal search finds the matching SME only.
        $this->actingAs($admin)->get('/admin/dashboard?search=Alpha')
            ->assertSee('Alpha Trading')
            ->assertDontSee('Beta Bakery');

        // No database error, and the injected condition does not match every SME.
        $response->assertOk();
        $response->assertDontSee('Alpha Trading');
        $response->assertDontSee('Beta Bakery');
        $this->assertDatabaseCount('users', 3);
    }

    public function test_script_in_a_business_name_is_escaped_not_executed(): void
    {
        $owner = User::factory()->create([
            'role' => 'sme_owner',
            'business_name' => '<script>alert(1)</script>',
        ]);

        $response = $this->actingAs($owner)->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
