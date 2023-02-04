<?php

namespace KieranFYI\Tests\Media;

use Illuminate\Foundation\Application;
use JeroenNoten\LaravelAdminLte\AdminLteServiceProvider;
use KieranFYI\Admin\Providers\AdminPackageServiceProvider;
use KieranFYI\Logging\Providers\LoggingPackageServiceProvider;
use KieranFYI\Media\Core\Providers\MediaCorePackageServiceProvider;
use KieranFYI\Media\Providers\MediaPackageServiceProvider;
use KieranFYI\Misc\Providers\MiscPackageServiceProvider;
use KieranFYI\Roles\Core\Providers\RolesCorePackageServiceProvider;
use KieranFYI\Services\Core\Providers\ServicesCorePackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Load package service provider.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MiscPackageServiceProvider::class,
            LoggingPackageServiceProvider::class,
            ServicesCorePackageServiceProvider::class,
            RolesCorePackageServiceProvider::class,
            MediaCorePackageServiceProvider::class,
            AdminLteServiceProvider::class,
            AdminPackageServiceProvider::class,
            MediaPackageServiceProvider::class
        ];
    }
}