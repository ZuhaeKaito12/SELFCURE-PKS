<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_sme_owner_cannot_open_administrator_pages(): void
    {
        $owner = User::factory()->create(['role' => 'sme_owner']);

        $this->actingAs($owner)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($owner)->get('/admin/reports/summary')->assertForbidden();
    }

    public function test_administrator_cannot_open_sme_owner_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/assessment/start')->assertForbidden();
        $this->actingAs($admin)->get('/action-plan')->assertForbidden();
    }

    public function test_sme_owner_cannot_open_another_smes_assessment(): void
    {
        $first = User::factory()->create(['role' => 'sme_owner']);
        $second = User::factory()->create(['role' => 'sme_owner']);
        $assessment = $first->assessments()->create(['status' => 'completed', 'overall_score' => 50, 'overall_level' => 'Developing', 'completed_at' => now()]);

        $this->actingAs($second)->get("/assessment/{$assessment->id}/result")->assertForbidden();
        $this->actingAs($second)->get("/assessment/{$assessment->id}/report")->assertForbidden();
    }

    public function test_login_is_refused_when_the_portal_tab_does_not_match_the_role(): void
    {
        $owner = User::factory()->create(['role' => 'sme_owner']);

        $this->post('/login', ['email' => $owner->email, 'password' => 'password', 'portal' => 'admin'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_locked_after_five_failed_attempts(): void
    {
        $owner = User::factory()->create(['role' => 'sme_owner']);

        foreach (range(1, 5) as $attempt) {
            $this->post('/login', ['email' => $owner->email, 'password' => 'wrong-password', 'portal' => 'sme_owner']);
        }

        // Even the correct password is refused while the lock is active.
        $response = $this->post('/login', ['email' => $owner->email, 'password' => 'password', 'portal' => 'sme_owner']);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_weak_passwords_are_rejected_at_registration(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'business_name' => 'Test Enterprise',
            'email' => 'weak@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'weak@example.com']);
    }

    public function test_forgot_password_gives_the_same_reply_for_unknown_emails(): void
    {
        User::factory()->create(['email' => 'known@example.com']);

        $known = $this->post('/forgot-password', ['email' => 'known@example.com']);
        $unknown = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        $known->assertSessionHas('status');
        $unknown->assertSessionHas('status', session('status'));
        $unknown->assertSessionHasNoErrors();
    }
}
