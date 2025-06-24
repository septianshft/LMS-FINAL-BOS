<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OptimizePerformance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Start performance timing
        $startTime = microtime(true);

        // Enable query logging only in debug mode
        if (config('app.debug', false)) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        // Log performance metrics
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        if (config('app.debug', false)) {
            $queryCount = count(DB::getQueryLog());

            // Log slow requests (over 500ms)
            if ($executionTime > 500) {
                Log::warning('Slow request detected', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'execution_time' => round($executionTime, 2) . 'ms',
                    'query_count' => $queryCount,
                    'memory_usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB'
                ]);
            }
        }

        // Add performance headers for debugging
        if (config('app.debug', false)) {
            $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB');
        }

        return $response;
    }
}
