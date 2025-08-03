<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            // Index for XP-based queries and leaderboards
            $table->index('total_xp', 'users_total_xp_index');
            
            // Index for group-based queries
            $table->index('group_id', 'users_group_id_index');
            
            // Composite index for admin queries
            $table->index(['is_admin', 'created_at'], 'users_admin_created_index');
        });

        // Stats table indexes (heavily queried for dashboard and analytics)
        Schema::table('stats', function (Blueprint $table) {
            // Primary query pattern: user_id + created_at for date ranges
            $table->index(['user_id', 'created_at'], 'stats_user_created_index');
            
            // Index for daily tracking checks
            $table->index(['user_id', 'created_at'], 'stats_user_date_index');
            
            // Index for mood analysis queries
            $table->index(['user_id', 'mood'], 'stats_user_mood_index');
            
            // Index for activity analysis
            $table->index(['user_id', 'activity'], 'stats_user_activity_index');
            
            // Index for productivity analysis
            $table->index(['user_id', 'productivity'], 'stats_user_productivity_index');
            
            // Index for weekly/monthly analysis
            $table->index('created_at', 'stats_created_at_index');
        });

        // User tickets table indexes (frequently checked for access control)
        Schema::table('user_tickets', function (Blueprint $table) {
            // Primary query pattern: user_id + ticket_type + expires_at
            $table->index(['user_id', 'ticket_type', 'expires_at'], 'user_tickets_access_index');
            
            // Index for ticket consumption queries
            $table->index(['user_id', 'ticket_type', 'remaining_value'], 'user_tickets_consumption_index');
            
            // Index for expiration cleanup jobs
            $table->index('expires_at', 'user_tickets_expires_index');
            
            // Index for ticket type queries
            $table->index('ticket_type', 'user_tickets_type_index');
        });

        // User memberships table indexes
        Schema::table('user_memberships', function (Blueprint $table) {
            // Primary query pattern: user_id + expires_at + status
            $table->index(['user_id', 'expires_at'], 'user_memberships_active_index');
            
            // Index for membership type queries
            $table->index(['user_id', 'membership_id'], 'user_memberships_user_type_index');
            
            // Index for expiration cleanup jobs
            $table->index('expires_at', 'user_memberships_expires_index');
            
            // Index for purchase history
            $table->index(['user_id', 'created_at'], 'user_memberships_history_index');
        });

        // Chat sessions table indexes
        Schema::table('chat_sessions', function (Blueprint $table) {
            // Index for user's session history
            $table->index(['user_id', 'created_at'], 'chat_sessions_user_history_index');
            
            // Index for professional's sessions
            $table->index(['professional_id', 'created_at'], 'chat_sessions_professional_index');
            
            // Index for status-based queries
            $table->index(['status', 'created_at'], 'chat_sessions_status_index');
            
            // Index for date range queries
            $table->index(['start', 'end'], 'chat_sessions_time_range_index');
        });

        // Messages table indexes
        Schema::table('messages', function (Blueprint $table) {
            // Index for session messages
            $table->index(['session_id', 'created_at'], 'messages_session_index');
            
            // Index for user's message history
            $table->index(['user_id', 'created_at'], 'messages_user_index');
        });

        // Mission completions table indexes
        Schema::table('mission_completions', function (Blueprint $table) {
            // Index for user's completion history
            $table->index(['user_id', 'completed_at'], 'mission_completions_user_index');
            
            // Index for daily completion checks
            $table->index(['user_id', 'mission_id', 'completed_at'], 'mission_completions_daily_index');
            
            // Index for mission statistics
            $table->index(['mission_id', 'completed_at'], 'mission_completions_mission_index');
        });

        // Chatbot sessions table indexes
        Schema::table('chatbot_sessions', function (Blueprint $table) {
            // Index for user's chatbot sessions
            $table->index(['user_id', 'created_at'], 'chatbot_sessions_user_index');
            
            // Index for session cleanup
            $table->index('expires_at', 'chatbot_sessions_expires_index');
        });

        // Chatbot messages table indexes
        Schema::table('chatbot_messages', function (Blueprint $table) {
            // Index for session messages
            $table->index(['session_id', 'created_at'], 'chatbot_messages_session_index');
        });

        // Sgd groups table indexes
        Schema::table('sgd_groups', function (Blueprint $table) {
            // Index for active groups
            $table->index(['status', 'created_at'], 'sgd_groups_status_index');
            
            // Index for host queries
            $table->index('host_id', 'sgd_groups_host_index');
        });

        // Sgd group user table indexes
        Schema::table('sgd_group_user', function (Blueprint $table) {
            // Index for user's group memberships
            $table->index(['user_id', 'created_at'], 'sgd_group_user_membership_index');
            
            // Index for group members
            $table->index(['sgd_group_id', 'created_at'], 'sgd_group_user_group_index');
        });

        // Daily XP logs table indexes
        Schema::table('daily_xp_logs', function (Blueprint $table) {
            // Index for user's XP history
            $table->index(['user_id', 'created_at'], 'daily_xp_logs_user_index');
            
            // Index for activity-based queries
            $table->index(['user_id', 'activity'], 'daily_xp_logs_activity_index');
        });

        // Weekly stats table indexes
        Schema::table('weekly_stats', function (Blueprint $table) {
            // Index for user's weekly stats
            $table->index(['user_id', 'week_start'], 'weekly_stats_user_index');
        });

        // Monthly stats table indexes
        Schema::table('monthly_stats', function (Blueprint $table) {
            // Index for user's monthly stats
            $table->index(['user_id', 'month_start'], 'monthly_stats_user_index');
        });

        // Mental health test results table indexes
        Schema::table('mental_health_test_results', function (Blueprint $table) {
            // Index for user's test history
            $table->index(['user_id', 'created_at'], 'mental_health_results_user_index');
        });

        // Professionals table indexes
        Schema::table('professionals', function (Blueprint $table) {
            // Index for active professionals
            $table->index(['status', 'created_at'], 'professionals_status_index');
            
            // Index for specialization queries
            $table->index('specialization', 'professionals_specialization_index');
        });

        // Missions table indexes
        Schema::table('missions', function (Blueprint $table) {
            // Index for active missions
            $table->index(['is_active', 'created_at'], 'missions_active_index');
            
            // Index for mission type queries
            $table->index('type', 'missions_type_index');
        });

        // Cards table indexes
        Schema::table('cards', function (Blueprint $table) {
            // Index for category-based queries
            $table->index('category', 'cards_category_index');
            
            // Index for difficulty-based queries
            $table->index('difficulty', 'cards_difficulty_index');
            
            // Index for active cards
            $table->index(['is_active', 'created_at'], 'cards_active_index');
        });

        // Quotes table indexes
        Schema::table('quotes', function (Blueprint $table) {
            // Index for random quote selection
            $table->index(['is_active', 'created_at'], 'quotes_active_index');
        });

        // Agenda table indexes
        Schema::table('agendas', function (Blueprint $table) {
            // Index for date-based queries
            $table->index(['date', 'created_at'], 'agendas_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_total_xp_index');
            $table->dropIndex('users_group_id_index');
            $table->dropIndex('users_admin_created_index');
        });

        // Stats table
        Schema::table('stats', function (Blueprint $table) {
            $table->dropIndex('stats_user_created_index');
            $table->dropIndex('stats_user_date_index');
            $table->dropIndex('stats_user_mood_index');
            $table->dropIndex('stats_user_activity_index');
            $table->dropIndex('stats_user_productivity_index');
            $table->dropIndex('stats_created_at_index');
        });

        // User tickets table
        Schema::table('user_tickets', function (Blueprint $table) {
            $table->dropIndex('user_tickets_access_index');
            $table->dropIndex('user_tickets_consumption_index');
            $table->dropIndex('user_tickets_expires_index');
            $table->dropIndex('user_tickets_type_index');
        });

        // User memberships table
        Schema::table('user_memberships', function (Blueprint $table) {
            $table->dropIndex('user_memberships_active_index');
            $table->dropIndex('user_memberships_user_type_index');
            $table->dropIndex('user_memberships_expires_index');
            $table->dropIndex('user_memberships_history_index');
        });

        // Chat sessions table
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropIndex('chat_sessions_user_history_index');
            $table->dropIndex('chat_sessions_professional_index');
            $table->dropIndex('chat_sessions_status_index');
            $table->dropIndex('chat_sessions_time_range_index');
        });

        // Messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_session_index');
            $table->dropIndex('messages_user_index');
        });

        // Mission completions table
        Schema::table('mission_completions', function (Blueprint $table) {
            $table->dropIndex('mission_completions_user_index');
            $table->dropIndex('mission_completions_daily_index');
            $table->dropIndex('mission_completions_mission_index');
        });

        // Chatbot sessions table
        Schema::table('chatbot_sessions', function (Blueprint $table) {
            $table->dropIndex('chatbot_sessions_user_index');
            $table->dropIndex('chatbot_sessions_expires_index');
        });

        // Chatbot messages table
        Schema::table('chatbot_messages', function (Blueprint $table) {
            $table->dropIndex('chatbot_messages_session_index');
        });

        // Sgd groups table
        Schema::table('sgd_groups', function (Blueprint $table) {
            $table->dropIndex('sgd_groups_status_index');
            $table->dropIndex('sgd_groups_host_index');
        });

        // Sgd group user table
        Schema::table('sgd_group_user', function (Blueprint $table) {
            $table->dropIndex('sgd_group_user_membership_index');
            $table->dropIndex('sgd_group_user_group_index');
        });

        // Daily XP logs table
        Schema::table('daily_xp_logs', function (Blueprint $table) {
            $table->dropIndex('daily_xp_logs_user_index');
            $table->dropIndex('daily_xp_logs_activity_index');
        });

        // Weekly stats table
        Schema::table('weekly_stats', function (Blueprint $table) {
            $table->dropIndex('weekly_stats_user_index');
        });

        // Monthly stats table
        Schema::table('monthly_stats', function (Blueprint $table) {
            $table->dropIndex('monthly_stats_user_index');
        });

        // Mental health test results table
        Schema::table('mental_health_test_results', function (Blueprint $table) {
            $table->dropIndex('mental_health_results_user_index');
        });

        // Professionals table
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropIndex('professionals_status_index');
            $table->dropIndex('professionals_specialization_index');
        });

        // Missions table
        Schema::table('missions', function (Blueprint $table) {
            $table->dropIndex('missions_active_index');
            $table->dropIndex('missions_type_index');
        });

        // Cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex('cards_category_index');
            $table->dropIndex('cards_difficulty_index');
            $table->dropIndex('cards_active_index');
        });

        // Quotes table
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex('quotes_active_index');
        });

        // Agenda table
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropIndex('agendas_date_index');
        });
    }
}; 