<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OptimizeDatabase extends Command
{
    protected $signature = 'db:optimize {--clear-cache : Clear all caches}';
    protected $description = 'Optimize database performance and clear caches';

    public function handle()
    {
        $this->info('🚀 Starting database optimization...');

        if ($this->option('clear-cache')) {
            $this->info('🧹 Clearing caches...');
            Cache::flush();
            $this->info('✅ Caches cleared successfully');
        }

        // Optimize MySQL tables
        $this->info('🔧 Optimizing database tables...');

        $tables = ['users', 'talents', 'recruiters', 'talent_requests', 'projects', 'talent_assignments'];

        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $this->info("✅ Optimized table: {$table}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to optimize table {$table}: " . $e->getMessage());
            }
        }

        // Update table statistics
        $this->info('📊 Updating table statistics...');
        try {
            DB::statement('ANALYZE TABLE users, talents, recruiters, talent_requests, projects, talent_assignments');
            $this->info('✅ Table statistics updated');
        } catch (\Exception $e) {
            $this->error('❌ Failed to update statistics: ' . $e->getMessage());
        }

        // Warm up critical caches
        $this->info('🔥 Warming up caches...');

        try {
            // Warm dashboard stats cache
            Cache::remember('talent_admin_dashboard_stats', 1800, function () {
                return DB::select('SELECT COUNT(*) as count FROM users')[0];
            });

            $this->info('✅ Dashboard cache warmed');
        } catch (\Exception $e) {
            $this->error('❌ Failed to warm cache: ' . $e->getMessage());
        }

        $this->info('🎉 Database optimization completed successfully!');

        return 0;
    }
}
