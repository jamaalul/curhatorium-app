<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Membership;
use App\Models\MembershipTicket;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memberships data
        $memberships = [
            [
                'name' => 'Calm Starter',
                'price' => 0,
                'duration_days' => 30,
                'description' => "+ Unlimited mental test\n+ 7 days Mood and Productivity Tracker\n+ 2 hours Ment-AI chatbot\n+ 7 days Missions of the day\n+ 1 time support group discussion\n+ 30 times deep cards",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'mental_test', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'tracker', 'limit_type' => 'day', 'limit_value' => 7],
                    ['ticket_type' => 'missions', 'limit_type' => 'day', 'limit_value' => 7],
                    ['ticket_type' => 'support_group', 'limit_type' => 'count', 'limit_value' => 1],
                ],
            ],
            [
                'name' => 'Growth Path',
                'price' => 23900,
                'duration_days' => 30,
                'description' => "+ Unlimited mental test\n+ 1 time share and talk via chat (w/ rangers)\n+ Unlimited deep cards\n+ 1 hour ment-AI chatbot\n+ 1 time support group discussion\n+ Unlimited Missions of The Day\n+ Unlimited Mood and Productivity Tracker",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'mental_test', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'support_group', 'limit_type' => 'count', 'limit_value' => 1],
                    ['ticket_type' => 'missions', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'tracker', 'limit_type' => 'unlimited', 'limit_value' => null],
                ],
            ],
            [
                'name' => 'Blossom',
                'price' => 59900,
                'duration_days' => 30,
                'description' => "+ Unlimited Mental Health test\n+ 2 times share and talk via chat (w/ rangers)\n+ Unlimited Mood and Productivity Tracker\n+ 3 times support group discussion\n+ Unlimited ment-AI chatbot\n+ Unlimited Missions of The Day\n+ Unlimited Deep Cards",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'mental_test', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'share_talk_ranger_chat', 'limit_type' => 'count', 'limit_value' => 2],
                    ['ticket_type' => 'tracker', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'support_group', 'limit_type' => 'count', 'limit_value' => 3],
                    ['ticket_type' => 'missions', 'limit_type' => 'unlimited', 'limit_value' => null],
                ],
            ],
            [
                'name' => 'Inner Peace',
                'price' => 86900,
                'duration_days' => 30,
                'description' => "+ Unlimited Mental test\n+ 3 times share and talk via chat (w/ rangers)\n+ 5 times support group discussion\n+ Unlimited Mood and Productivity Tracker (Extended report)\n+ Unlimited ment-AI chatbot\n+ Unlimited Missions of The Day\n+ Unlimited Deep Cards",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'mental_test', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'share_talk_ranger_chat', 'limit_type' => 'count', 'limit_value' => 3],
                    ['ticket_type' => 'support_group', 'limit_type' => 'count', 'limit_value' => 5],
                    ['ticket_type' => 'tracker', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'missions', 'limit_type' => 'unlimited', 'limit_value' => null],
                ],
            ],
            [
                'name' => 'Harmony',
                'price' => 19900,
                'duration_days' => 0,
                'description' => "+ 1 time support group discussion\n+ Unlimited deep cards",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'support_group', 'limit_type' => 'count', 'limit_value' => 1],
                ],
            ],
            [
                'name' => 'Serenity',
                'price' => 15000,
                'duration_days' => 0,
                'description' => "+ 1 time share and talk (w/ rangers)\n+ Unlimited deep cards",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'share_talk_ranger_chat', 'limit_type' => 'count', 'limit_value' => 1],
                ],
            ],
            [
                'name' => "Chat with Sanny's Aid",
                'price' => 77900,
                'duration_days' => 0,
                'description' => "+ 1 time share and talk via chat (w/ psychiatrist)\n+ Unlimited Mood and Productivity Tracker (expire after 1 month)\n+ Unlimited Deep Cards (expire after 1 month)",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'share_talk_psy_chat', 'limit_type' => 'count', 'limit_value' => 1],
                    ['ticket_type' => 'tracker', 'limit_type' => 'unlimited', 'limit_value' => null],
                ],
            ],
            [
                'name' => "Meet with Sanny's Aid",
                'price' => 199000,
                'duration_days' => 0,
                'description' => "+ Unlimited Mood and Productivity Tracker (expire after 1 month)\n+ 1 time share and talk via video call (w/ psychiatrist)\n+ Unlimited Deep Cards (expire after 1 month)",
                'is_active' => true,
                'tickets' => [
                    ['ticket_type' => 'tracker', 'limit_type' => 'unlimited', 'limit_value' => null],
                    ['ticket_type' => 'share_talk_psy_video', 'limit_type' => 'count', 'limit_value' => 1],
                ],
            ],
        ];

        foreach ($memberships as $m) {
            $tickets = $m['tickets'];
            unset($m['tickets']);
            $membership = Membership::create($m);
            foreach ($tickets as $ticket) {
                MembershipTicket::create(array_merge($ticket, ['membership_id' => $membership->id]));
            }
        }
    }
}
