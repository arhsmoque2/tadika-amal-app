<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class InfrastructureConfigTest extends TestCase
{
    public function test_database_configuration_has_pgsql_and_hermetic_sqlite(): void
    {
        $connections = Config::get('database.connections');

        $this->assertArrayHasKey('pgsql', $connections, 'Postgres connection configuration must exist for Neon');
        $this->assertSame('pgsql', $connections['pgsql']['driver'] ?? null);
        $this->assertArrayHasKey('url', $connections['pgsql'], 'pgsql connection must define url key for DATABASE_URL mapping');

        $this->assertArrayHasKey('sqlite', $connections, 'SQLite connection must exist for hermetic test execution');
    }

    public function test_cloud_run_stateless_session_and_cache_drivers_declared(): void
    {
        $sessionDrivers = ['database', 'redis', 'array', 'cookie'];
        $currentSessionDriver = Config::get('session.driver');

        $this->assertContains(
            $currentSessionDriver,
            $sessionDrivers,
            'Session driver must be stateless / externalized for Cloud Run scalability'
        );

        $cacheStores = Config::get('cache.stores');
        $this->assertArrayHasKey('database', $cacheStores, 'Database cache store must be configured');
        $this->assertArrayHasKey('array', $cacheStores, 'Array cache store must be configured for unit testing');
    }
}
