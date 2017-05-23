<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        putenv('APP_URL='.$this->baseUrl);
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

    /**
     * Check if data or a subset of data is contained within an array.
     *
     * @param array $expectedData
     * @param $actualData
     *
     * @return $this
     */
    public function assertDataContains(array $expectedData, $actualData)
    {
        $actual = json_encode(Arr::sortRecursive(
            (array) $actualData
        ));

        foreach (Arr::sortRecursive($expectedData) as $key => $value) {
            $expected = substr(json_encode([$key => $value]), 1, -1);

            $this->assertTrue(
                Str::contains($actual, $expected),
                'Unable to find JSON fragment: '.PHP_EOL.PHP_EOL.
                "[{$expected}]".PHP_EOL.PHP_EOL.
                'within'.PHP_EOL.PHP_EOL.
                "[{$actual}]."
            );
        }

        return $this;
    }
}
