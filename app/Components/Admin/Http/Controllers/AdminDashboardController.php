<?php

namespace App\Components\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\ResourceType;
use App\Models\ResourceTypeMapping;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // System Statistics
        $systemStats = [
            'users_count' => User::count(),
            'roles_count' => Role::count(),
            'user_roles_count' => DB::table('auth.user_roles')->count(),
            'resources_count' => ResourceType::count(),
            'resource_mappings_count' => ResourceTypeMapping::count(),
        ];

        // Database Statistics
        $dbStats = DB::select("
            SELECT
                pg_size_pretty(pg_database_size(current_database())) as db_size,
                (SELECT count(*) FROM information_schema.schemata
                 WHERE schema_name NOT IN ('information_schema', 'pg_catalog')) as schema_count,
                (SELECT count(*) FROM information_schema.tables
                 WHERE table_schema NOT IN ('information_schema', 'pg_catalog')
                 AND table_type = 'BASE TABLE') as table_count,
                (SELECT count(*) FROM information_schema.views
                 WHERE table_schema NOT IN ('information_schema', 'pg_catalog')
                 AND table_name NOT LIKE 'pg_%') as view_count,
                (SELECT count(*) FROM pg_matviews) as materialized_view_count,
                (SELECT count(*) FROM pg_indexes
                 WHERE schemaname NOT IN ('information_schema', 'pg_catalog')) as index_count
        ");

        return view('admin::admin.dashboard', compact('systemStats', 'dbStats'));
    }

    public function getDatabaseIO(): JsonResponse
    {
        // Get current stats
        $stats = DB::select("
            SELECT
                blks_read,
                blks_hit,
                tup_returned,
                tup_fetched,
                tup_inserted,
                tup_updated,
                tup_deleted,
                EXTRACT(EPOCH FROM now()) as timestamp,
                xact_commit,
                xact_rollback
            FROM pg_stat_database
            WHERE datname = current_database()
        ");

        // Store the stats in cache with timestamp
        $previousStats = cache()->get('database_stats');
        $currentStats = $stats[0];
        cache()->put('database_stats', $currentStats, now()->addMinutes(5));

        // If we have previous stats, calculate the differences
        if ($previousStats) {
            $timeDiff = $currentStats->timestamp - $previousStats->timestamp;

            $response = [
                // Calculate rates per second
                'blks_read' => ($currentStats->blks_read - $previousStats->blks_read) / $timeDiff,
                'blks_hit' => ($currentStats->blks_hit - $previousStats->blks_hit) / $timeDiff,
                'tup_returned' => ($currentStats->tup_returned - $previousStats->tup_returned) / $timeDiff,
                'tup_inserted' => ($currentStats->tup_inserted - $previousStats->tup_inserted) / $timeDiff,
                'tup_updated' => ($currentStats->tup_updated - $previousStats->tup_updated) / $timeDiff,
                'tup_deleted' => ($currentStats->tup_deleted - $previousStats->tup_deleted) / $timeDiff,
                'xact_commit' => ($currentStats->xact_commit - $previousStats->xact_commit) / $timeDiff,
                'xact_rollback' => ($currentStats->xact_rollback - $previousStats->xact_rollback) / $timeDiff,
            ];
        } else {
            // For the first call, return zeros
            $response = [
                'blks_read' => 0,
                'blks_hit' => 0,
                'tup_returned' => 0,
                'tup_inserted' => 0,
                'tup_updated' => 0,
                'tup_deleted' => 0,
                'xact_commit' => 0,
                'xact_rollback' => 0,
            ];
        }

        return response()->json($response);
    }
}
