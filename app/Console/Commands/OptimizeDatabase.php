<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseOptimizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize {--tables : Optimize database tables} {--cache : Warm up cache} {--indexes : Check index usage} {--stats : Show database statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database performance by adding indexes, optimizing tables, and warming up cache';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseOptimizationService $optimizationService): int
    {
        $this->info('🚀 Starting database optimization...');

        try {
            // Enable query logging for analysis
            DB::enableQueryLog();

            if ($this->option('tables')) {
                $this->optimizeTables($optimizationService);
            }

            if ($this->option('cache')) {
                $this->warmUpCache($optimizationService);
            }

            if ($this->option('indexes')) {
                $this->checkIndexUsage();
            }

            if ($this->option('stats')) {
                $this->showStatistics($optimizationService);
            }

            // If no specific option is provided, run all optimizations
            if (!$this->option('tables') && !$this->option('cache') && !$this->option('indexes') && !$this->option('stats')) {
                $this->optimizeTables($optimizationService);
                $this->warmUpCache($optimizationService);
                $this->checkIndexUsage();
                $this->showStatistics($optimizationService);
            }

            $this->info('✅ Database optimization completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Database optimization failed: ' . $e->getMessage());
            Log::error('Database optimization command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Optimize database tables
     */
    private function optimizeTables(DatabaseOptimizationService $optimizationService): void
    {
        $this->info('📊 Optimizing database tables...');
        
        $results = $optimizationService->optimizeTables();
        
        $this->table(
            ['Table', 'Status'],
            collect($results)->map(fn($status, $table) => [$table, $status])->toArray()
        );
    }

    /**
     * Warm up cache
     */
    private function warmUpCache(DatabaseOptimizationService $optimizationService): void
    {
        $this->info('🔥 Warming up cache...');
        
        $optimizationService->warmUpCache();
        
        $this->info('✅ Cache warmed up successfully');
    }

    /**
     * Check index usage
     */
    private function checkIndexUsage(): void
    {
        $this->info('🔍 Checking index usage...');
        
        try {
            // Get index usage statistics
            $indexStats = DB::select("
                SELECT 
                    TABLE_NAME,
                    INDEX_NAME,
                    CARDINALITY,
                    SUB_PART,
                    PACKED,
                    NULLABLE,
                    INDEX_TYPE
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE()
                ORDER BY TABLE_NAME, INDEX_NAME
            ");

            if (empty($indexStats)) {
                $this->warn('No index statistics available');
                return;
            }

            $this->table(
                ['Table', 'Index', 'Cardinality', 'Type'],
                collect($indexStats)->map(function ($stat) {
                    return [
                        $stat->TABLE_NAME,
                        $stat->INDEX_NAME,
                        $stat->CARDINALITY ?? 'N/A',
                        $stat->INDEX_TYPE
                    ];
                })->toArray()
            );

        } catch (\Exception $e) {
            $this->warn('Could not retrieve index statistics: ' . $e->getMessage());
        }
    }

    /**
     * Show database statistics
     */
    private function showStatistics(DatabaseOptimizationService $optimizationService): void
    {
        $this->info('📈 Database Statistics:');

        // Query statistics
        $queryStats = $optimizationService->getQueryStatistics();
        $this->line("Total Queries: {$queryStats['total_queries']}");
        $this->line("Slow Queries: {$queryStats['slow_queries']}");

        // Cache statistics
        $cacheStats = $optimizationService->getCacheStatistics();
        $this->line("Cache Keys: {$cacheStats['total_keys']}");
        $this->line("Memory Usage: {$cacheStats['memory_usage']}");
        $this->line("Hit Rate: {$cacheStats['hit_rate']}%");

        // Table sizes
        $this->showTableSizes();
    }

    /**
     * Show table sizes
     */
    private function showTableSizes(): void
    {
        $this->info('📋 Table Sizes:');

        try {
            $tableSizes = DB::select("
                SELECT 
                    TABLE_NAME,
                    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)',
                    TABLE_ROWS
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE()
                ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            ");

            if (empty($tableSizes)) {
                $this->warn('No table size information available');
                return;
            }

            $this->table(
                ['Table', 'Size (MB)', 'Rows'],
                collect($tableSizes)->map(function ($table) {
                    return [
                        $table->TABLE_NAME,
                        $table->{'Size (MB)'},
                        number_format($table->TABLE_ROWS)
                    ];
                })->toArray()
            );

        } catch (\Exception $e) {
            $this->warn('Could not retrieve table sizes: ' . $e->getMessage());
        }
    }
} 