<?php
// tests/Feature/Components/Api/Traits/HandlesCustomSchemas.php

namespace Tests\Feature\Components\Api\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;

trait HandlesCustomSchemas
{
    use InteractsWithConsole;

    /**
     * Clean up custom schemas before fresh migration
     */
    protected function cleanSchemas(): void
    {
        // Drop only custom schemas
        $customSchemas = ['auth', 'org'];
        foreach ($customSchemas as $schema) {
            DB::statement("DROP SCHEMA IF EXISTS {$schema} CASCADE");
        }

        // Clean public schema tables but don't drop the schema
        DB::statement("TRUNCATE TABLE public.users CASCADE");
        DB::statement("TRUNCATE TABLE public.migrations CASCADE");
        DB::statement("TRUNCATE TABLE public.failed_jobs CASCADE");
        DB::statement("TRUNCATE TABLE public.password_reset_tokens CASCADE");
        DB::statement("TRUNCATE TABLE public.personal_access_tokens CASCADE");

        // Recreate custom schemas
        foreach ($customSchemas as $schema) {
            DB::statement("CREATE SCHEMA IF NOT EXISTS {$schema}");
        }
    }

    /**
     * Run custom schema migrations
     */
    protected function runCustomMigrations(): void
    {
        $this->cleanSchemas();

        $paths = [
            'database/migrations/public',
            'database/migrations/auth',
            'database/migrations/org',
        ];

        foreach ($paths as $path) {
            $this->artisan('migrate', [
                '--path' => $path,
                '--database' => 'pgsql',
                '--force' => true,
            ]);
        }
    }
}
