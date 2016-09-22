<?php


/**
 * Class TestCase.
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [\TalvBansal\MediaManager\Providers\MediaManagerServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = realpath(__DIR__.'../src');

        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
            'strict'   => false,
        ]);

        //create temp folder
        $app['config']->set('filesystems.disks.public', [
            'driver' => 'local',
            'root'   => $this->getStoragePath(),
        ]);
    }

    public function getTempDirectory($suffix = '')
    {
        return __DIR__.'/temp'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getPublicDir($suffix = '')
    {
        return $this->getTempDirectory().'/app/public'.($suffix == '' ? '' : '/'.$suffix);
    }

    public function getStoragePath($path = 'app')
    {
        return realpath(storage_path($path));
    }
}
