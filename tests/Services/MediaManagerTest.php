<?php

use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: talv
 * Date: 30/08/16
 * Time: 23:42.
 */
class MediaManagerTest extends TestCase
{
    /**
     * @var \TalvBansal\MediaManager\Services\MediaManager
     */
    private $mediaManager;

    /** @var array */
    private $filesCreated = [];

    /** @var array */
    private $foldersCreated = [];

    public function setUp()
    {
        parent::setUp();
        $this->mediaManager = new  \TalvBansal\MediaManager\Services\MediaManager(new \Dflydev\ApacheMimeTypes\PhpRepository());
    }

    /**
     * clean up any files and folder that have been created through the testing process.
     */
    public function tearDown()
    {
        collect($this->filesCreated)->each(function ($file) {
            try {
                unlink($this->getStoragePath().$file);
            } catch (Exception $e) {
                //dump( realpath(storage_path('app')) . $file );
                //dump($e->getMessage());
            }
        });

        collect($this->foldersCreated)->each(function ($folder) {
            try {
                rmdir($this->getStoragePath().$folder);
            } catch (Exception $e) {
                dump(realpath(storage_path('app')).$folder);
                dump($e->getMessage());
            }
        });

        parent::tearDown();
    }

    public function test_can_list_root_directory()
    {
        $response = $this->mediaManager->folderInfo('');

        $this->assertEquals($response, [
            'folder' => '/',
            'folderName' => 'Root',
            'breadcrumbs' => new Collection(),
            'subfolders' => new Collection(),
            'files' => new Collection(),
            'itemsCount' => 0
        ]);
    }

    public function test_can_create_a_file_in_root_directory()
    {
        $tmpFile = DIRECTORY_SEPARATOR.date('Y-m-d H:i:s').'.txt';
        $content = 'foo';
        $this->createAndTestFilesExistenceAndContent($tmpFile, $content);
    }

    public function test_can_create_a_file_in_subfolder()
    {
        $folder = DIRECTORY_SEPARATOR.'subfolder';
        $tmpFile = $folder.DIRECTORY_SEPARATOR.date('Y-m-d H:i:s').'.txt';
        $this->foldersCreated[] = $folder;
        $content = 'newfoo';
        $this->createAndTestFilesExistenceAndContent($tmpFile, $content);
    }

    public function test_can_create_folder()
    {
        $folderName = DIRECTORY_SEPARATOR.date('Y-m-d H:i:s');
        $this->foldersCreated[] = $folderName;

        // create
        $response = $this->mediaManager->createDirectory($folderName);

        // is created ?
        $this->assertTrue($response);

        // is found ?
        $list = $this->mediaManager->folderInfo('/');
        $subFolders = $list['subfolders'];

        $this->assertTrue(isset($subFolders[$folderName]));
    }

    public function test_can_delete_a_folder()
    {
        // create a folder
        $folderName = DIRECTORY_SEPARATOR.date('Y-m-d H:i:s');
        $response = $this->mediaManager->createDirectory($folderName);

        // is created ?
        $this->assertTrue($response);

        // is found ?
        $list = $this->mediaManager->folderInfo('/');
        $subFolders = $list['subfolders'];

        $this->assertTrue(isset($subFolders[$folderName]));

        // delete
        $response = $this->mediaManager->deleteDirectory($folderName);
        $this->assertTrue($response);
    }

    /**
     * @param $tmpFile
     * @param $content
     */
    protected function createAndTestFilesExistenceAndContent($tmpFile, $content)
    {
        $this->filesCreated[] = $tmpFile;
        // create
        $response = $this->mediaManager->saveFile($tmpFile, $content);

        // is created?
        $this->assertTrue($response);

        // is found?
        $folderPath = dirname($tmpFile);
        $folderInfo = $this->mediaManager->folderInfo($folderPath);
        $fileInfo = collect($folderInfo['files']);
        $this->assertTrue($fileInfo->contains('fullPath', $tmpFile));

        // contains content
        $file = $fileInfo->where('fullPath', $tmpFile)->first();
        $fileContents = file_get_contents($this->getStoragePath().$file['fullPath']);
        $this->assertEquals($fileContents, $content);
    }
}
