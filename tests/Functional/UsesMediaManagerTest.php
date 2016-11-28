<?php

/**
 * Created by PhpStorm.
 * User: talv
 * Date: 21/11/16
 * Time: 01:29.
 */
class UsesMediaManagerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Register Routes during acceptance tests...
        \TalvBansal\MediaManager\Routes\MediaRoutes::get();
    }

    public function test_can_list_files_from_the_media_manager_root()
    {
        $mediaManager = new \TalvBansal\MediaManager\Services\MediaManager(new \Dflydev\ApacheMimeTypes\PhpRepository());
        $mediaManager->createDirectory('/test');

        $this->visit('/admin/browser/index')->seeJson(
                [
                    'breadCrumbs' => [],
                    'files'       => [],
                    'folder'      => '/',
                    'folderName'  => 'Root',
                    'itemsCount'  => 1,
                    'subFolders'  => ['/test' => 'test'],
                ]
            );
    }
}
