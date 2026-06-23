<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use App\Models\PlanBenefit;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'id' => 1,
                'name' => 'Free',
                'price_idr' => '0.00',
                'billing_cycle' => 'monthly',
                'ai_window_hours' => 168,
                'benefits' => [
                    ['benefit' => 'snt_rgr_chat', 'amount' => 0],
                    ['benefit' => 'snt_psy_chat', 'amount' => 0],
                    ['benefit' => 'snt_psy_vc', 'amount' => 0],
                    ['benefit' => 'sgd', 'amount' => 0],
                    ['benefit' => 'ai_window_token', 'amount' => 25000],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Calm',
                'price_idr' => '49900.00',
                'billing_cycle' => 'monthly',
                'ai_window_hours' => 24,
                'benefits' => [
                    ['benefit' => 'snt_rgr_chat', 'amount' => 2],
                    ['benefit' => 'snt_psy_chat', 'amount' => 0],
                    ['benefit' => 'snt_psy_vc', 'amount' => 0],
                    ['benefit' => 'sgd', 'amount' => 2],
                    ['benefit' => 'ai_window_token', 'amount' => 13333],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Peaceful',
                'price_idr' => '169900.00',
                'billing_cycle' => 'monthly',
                'ai_window_hours' => 24,
                'benefits' => [
                    ['benefit' => 'snt_rgr_chat', 'amount' => 4],
                    ['benefit' => 'snt_psy_chat', 'amount' => 1],
                    ['benefit' => 'snt_psy_vc', 'amount' => 0],
                    ['benefit' => 'sgd', 'amount' => 4],
                    ['benefit' => 'ai_window_token', 'amount' => 66666],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $benefits = $planData['benefits'];
            unset($planData['benefits']);

            $plan = MembershipPlan::updateOrCreate(
                ['id' => $planData['id']],
                $planData
            );

            foreach ($benefits as $benefitData) {
                PlanBenefit::updateOrCreate(
                    [
                        'membership_plan_id' => $plan->id,
                        'benefit' => $benefitData['benefit'],
                    ],
                    $benefitData
                );
            }
        }
    }
}
