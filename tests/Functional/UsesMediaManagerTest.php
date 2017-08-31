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
        $mediaManager = app()->make(\TalvBansal\MediaManager\Services\MediaManager::class);
        $mediaManager->createDirectory('/test');

        $response = $this->get('/admin/browser/index');

        $response->assertJsonFragment(
            [
                'folder'      => '/',
                'folderName'  => 'Root',
                'breadCrumbs' => [],
                'files'       => [],
                'itemsCount'  => 1,
            ]
        );

        // for some reason i cant get the sub folder structure to assert properly in the test above...
        $response->assertJsonStructure([
            'folder',
            'folderName',
            'breadCrumbs',
            'files',
            'itemsCount',
            'subFolders',
        ]);

        $response->assertJsonFragment([
            'fullPath' => '/test',
            'mimeType' => 'folder',
            'name'     => 'test',
        ]);
    }
}
