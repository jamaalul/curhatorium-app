<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\MonthlyStat;
use App\Models\Stat;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WeeklyStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_tracker_entry_without_ai_feedback(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tracker.entry'), [
                'mood' => 8,
                'activity' => 'work',
                'activityExplanation' => 'Finished my project tasks today.',
                'energy' => 7,
                'productivity' => 9,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('tracker.result'));

        $this->assertDatabaseHas('stats', [
            'user_id' => $user->id,
            'mood' => 8,
            'activity' => 'work',
            'explanation' => 'Finished my project tasks today.',
            'energy' => 7,
            'productivity' => 9,
            'feedback' => null,
        ]);
    }

    public function test_result_page_does_not_contain_ai_feedback_section(): void
    {
        $user = User::factory()->create();

        $stat = Stat::create([
            'user_id' => $user->id,
            'mood' => 8,
            'activity' => 'work',
            'explanation' => 'Good day',
            'energy' => 7,
            'productivity' => 8,
            'day' => now()->format('l'),
            'feedback' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('tracker.result'));

        $response->assertOk();
        $response->assertSee('Laporan Hari Ini');
        $response->assertDontSee('Umpan Balik & Insight AI');
        $response->assertDontSee('Ment-AI');
        $response->assertDontSee('ai-analysis');
    }

    public function test_stat_detail_page_does_not_contain_ai_feedback_section(): void
    {
        $user = User::factory()->create();

        $stat = Stat::create([
            'user_id' => $user->id,
            'mood' => 7,
            'activity' => 'exercise',
            'explanation' => 'Went for a morning run',
            'energy' => 8,
            'productivity' => 7,
            'day' => now()->format('l'),
            'feedback' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('tracker.stat.detail', $stat->id));

        $response->assertOk();
        $response->assertSee('Detail Catatan Mood');
        $response->assertDontSee('Umpan Balik & Insight AI');
        $response->assertDontSee('Ment-AI');
        $response->assertDontSee('ai-analysis');
    }

    public function test_weekly_stat_detail_page_does_not_contain_ai_feedback_section(): void
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create(['id' => 1]);

        UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDays(7),
            'current_period_end' => now()->addDays(23),
        ]);

        $weeklyStat = WeeklyStat::create([
            'user_id' => $user->id,
            'week_start' => now()->subDays(6)->startOfDay(),
            'week_end' => now()->endOfDay(),
            'avg_mood' => 7.5,
            'avg_energy' => 7.0,
            'avg_productivity' => 8.0,
            'best_mood' => 9.0,
            'total_entries' => 7,
            'feedback' => 'Legacy weekly feedback',
        ]);

        Stat::create([
            'user_id' => $user->id,
            'mood' => 8,
            'activity' => 'work',
            'explanation' => 'Good work today',
            'energy' => 8,
            'productivity' => 8,
            'day' => now()->format('l'),
            'created_at' => now()->subDays(2),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('tracker.weekly-stat.detail', $weeklyStat->id));

        $response->assertOk();
        $response->assertSee('Ringkasan Mingguan');
        $response->assertDontSee('Umpan Balik & Insight AI Mingguan');
        $response->assertDontSee('Ment-AI');
    }

    public function test_monthly_stat_detail_page_does_not_contain_ai_feedback_section(): void
    {
        $user = User::factory()->create();
        $plan = MembershipPlan::factory()->create();

        UserSubscription::create([
            'user_id' => $user->id,
            'membership_plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now()->subDays(30),
            'current_period_end' => now()->addDays(30),
        ]);

        $monthlyStat = MonthlyStat::create([
            'user_id' => $user->id,
            'month' => now()->startOfMonth()->toDateString(),
            'month_start' => now()->startOfMonth()->toDateString(),
            'month_end' => now()->endOfMonth()->toDateString(),
            'avg_mood' => 7.5,
            'avg_productivity' => 8.0,
            'best_mood' => 9.0,
            'total_entries' => 30,
            'feedback' => 'Legacy monthly feedback',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('tracker.monthly-stat.detail', $monthlyStat->id));

        $response->assertOk();
        $response->assertSee('Ringkasan Bulanan');
        $response->assertDontSee('Umpan Balik & Insight AI Bulanan');
        $response->assertDontSee('Ment-AI');
    }
}
