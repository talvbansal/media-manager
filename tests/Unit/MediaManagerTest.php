<?php

use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: talv
 * Date: 30/08/16
 * Time: 23:42.
 *
 * @coversDefaultClass \TalvBansal\MediaManager\Services\MediaManager
 */
class MediaManagerTest extends TestCase
{
    /**
     * @var \TalvBansal\MediaManager\Services\MediaManager
     */
    private $mediaManager;

    public function setUp()
    {
        parent::setUp();
        $this->cleanUpStorageFolder();
        $this->mediaManager = new  \TalvBansal\MediaManager\Services\MediaManager(new \Dflydev\ApacheMimeTypes\PhpRepository());
    }

    /**
     * Clean up any files and folders that have been created through the testing process.
     */
    public function tearDown()
    {
        $this->cleanUpStorageFolder();
        parent::tearDown();
    }

    /**
     * Delete any temporary files and folders created during unit tests.
     */
    protected function cleanUpStorageFolder()
    {
        // Remove all created files...
        $allFiles = File::allFiles($this->getStoragePath('app'), true);
        /** @var SplFileInfo $file */
        foreach ($allFiles as $file) {
            unlink($file->getPathname());
        }

        // Remove all created folders...
        $allDirectories = Storage::allDirectories(realpath($this->getStoragePath().'/app'));
        foreach ($allDirectories as $folder) {
            try {
                rmdir(($this->getStoragePath().DIRECTORY_SEPARATOR.$folder));
            } catch (Exception $e) {
                File::deleteDirectory($folder);
            }
        }
    }

    /**
     * @param $uploadedFileName
     *
     * @return \Illuminate\Http\UploadedFile
     */
    protected function createNewFileToUpload($uploadedFileName)
    {
        $filePath = realpath(__DIR__.'/../20151106-_SKB7220.jpg');

        return new \Illuminate\Http\UploadedFile($filePath, $uploadedFileName, 'image/jpeg', filesize($filePath), null, true);
    }

    /**
     * @covers ::saveUploadedFiles
     *
     * @param array  $filesToCreate
     * @param string $path
     *
     * @return int
     */
    protected function createAndSaveFiles(array $filesToCreate, $path = '/')
    {
        $files = [];
        foreach ($filesToCreate as $fileName) {
            $files[] = $this->createNewFileToUpload($fileName);
        }
        $uploadedFiles = new \TalvBansal\MediaManager\Http\UploadedFiles($files);
        $response = $this->mediaManager->saveUploadedFiles($uploadedFiles, $path);
        $this->assertEquals(count($files), $response);

        return $response;
    }

    /**
     * @covers ::folderInfo()
     * @covers ::breadcrumbs()
     */
    public function test_can_list_root_directory()
    {
        $response = $this->mediaManager->folderInfo('');

        $this->assertEquals($response, [
            'folder'      => '/',
            'folderName'  => 'Root',
            'breadCrumbs' => new Collection(),
            'subFolders'  => new Collection(),
            'files'       => new Collection(),
            'itemsCount'  => 0,
        ]);
    }

    /**
     * @covers ::saveUploadedFiles
     */
    public function test_can_create_a_file_in_root_directory()
    {
        $count = $this->createAndSaveFiles(['example1.jpg']);

        $this->assertEquals(1, $count);
        $this->assertEquals(true, File::exists(storage_path('app').'/example1.jpg'));
    }

    /**
     * @covers ::saveUploadedFiles
     */
    public function test_can_create_multiple_files_in_the_root_directory()
    {
        $count = $this->createAndSaveFiles(['example1.jpg', 'example2.jpg']);

        $this->assertEquals(2, $count);
        $this->assertEquals(true, File::exists(storage_path('app').'/example1.jpg'));
        $this->assertEquals(true, File::exists(storage_path('app').'/example2.jpg'));
    }

    /**
     * @covers ::saveUploadedFiles
     */
    public function test_can_create_multiple_files_in_a_sub_folder()
    {
        $count = $this->createAndSaveFiles(['example1.jpg', 'example2.jpg'], '/test');

        $this->assertEquals(2, $count);
        $this->assertEquals(true, File::exists(storage_path('app').'/test/example1.jpg'));
        $this->assertEquals(true, File::exists(storage_path('app').'/test/example2.jpg'));
    }

    /**
     * @covers ::folderInfo()
     */
    public function test_does_not_list_hidden_files()
    {
        $count = $this->createAndSaveFiles(['.example1.jpg']);

        $this->assertEquals(1, $count);
        $response = $this->mediaManager->folderInfo('');

        $this->assertEquals($response, [
            'folder'      => '/',
            'folderName'  => 'Root',
            'breadCrumbs' => new Collection(),
            'subFolders'  => new Collection(),
            'files'       => new Collection(),
            'itemsCount'  => 0,
        ]);
    }

    /**
     * @covers ::createDirectory
     */
    public function test_can_create_folder()
    {
        $folderName = DIRECTORY_SEPARATOR.date('Y-m-d');

        // create
        $response = $this->mediaManager->createDirectory($folderName);

        // is created ?
        $this->assertTrue($response);

        // is found ?
        $list = $this->mediaManager->folderInfo('/');
        $subFolders = $list['subFolders'];

        $this->assertEquals($subFolders[0]['fullPath'], $folderName);
    }

    /**
     * @covers ::createDirectory
     * @covers ::errors
     */
    public function test_cannot_create_a_folder_that_already_exists()
    {
        $folderName = DIRECTORY_SEPARATOR.'FixedName';
        $response = $this->mediaManager->createDirectory($folderName);

        // is created ?
        $this->assertTrue($response);
        $this->assertEquals(true, File::exists(storage_path('app').$folderName));

        // try and create the same folder again
        $response = $this->mediaManager->createDirectory($folderName);
        $this->assertEquals(false, $response);
        $errors = $this->mediaManager->errors();
        $this->assertContains('Folder "'.$folderName.'" already exists.', $errors);
    }

    /**
     * @covers ::deleteDirectory
     */
    public function test_can_delete_a_folder()
    {
        // create a folder
        $folderName = DIRECTORY_SEPARATOR.date('Y-m-d');
        $response = $this->mediaManager->createDirectory($folderName);

        // is created ?
        $this->assertTrue($response);

        // delete
        $response = $this->mediaManager->deleteDirectory($folderName);
        $this->assertTrue($response);

        // is found ?
        $list = $this->mediaManager->folderInfo('/');
        $folders = collect($list['subFolders'])->map(function ($folder) {
            return $folder['fullPath'];
        })->toArray();

        $this->assertTrue(!in_array($folderName, $folders));
    }

    /**
     * @covers ::deleteDirectory
     */
    public function test_can_not_delete_a_folder_that_is_not_empty()
    {
        $this->createAndSaveFiles(['testItem.jpg'], '/testNotEmpty');

        // Try and delete a not empty folder
        $response = $this->mediaManager->deleteDirectory('/testNotEmpty');
        $this->assertFalse($response);

        $errors = $this->mediaManager->errors();
        $this->assertContains('The directory must be empty to delete it.', $errors);
    }

    /**
     * @covers ::deleteFile
     */
    public function test_can_delete_a_file()
    {
        $this->createAndSaveFiles(['test.jpg']);
        $response = $this->mediaManager->deleteFile('test.jpg');
        $this->assertTrue($response);
    }

    /**
     * @covers ::deleteFile
     */
    public function test_can_not_delete_a_file_that_does_not_exist()
    {
        $response = $this->mediaManager->deleteFile('doesnt-exist.jpg');
        $this->assertFalse($response);

        $errors = $this->mediaManager->errors();
        $this->assertContains('File does not exist.', $errors);
    }

    /**
     * @covers ::rename
     */
    public function test_can_rename_a_file()
    {
        $this->createAndSaveFiles(['test.jpg']);

        $response = $this->mediaManager->rename('/', 'test.jpg', 'renamed.jpg');
        $this->assertTrue($response);

        $this->assertEquals(true, File::exists(storage_path('app').'/renamed.jpg'));
    }

    /**
     * @covers ::rename
     */
    public function test_can_not_rename_a_file_to_a_files_that_already_exists()
    {
        $this->createAndSaveFiles(['test.jpg', 'renamed.jpg']);

        $response = $this->mediaManager->rename('/', 'test.jpg', 'renamed.jpg');
        $this->assertFalse($response);

        $errors = $this->mediaManager->errors();
        $this->assertContains('The file "renamed.jpg" already exists in this folder.', $errors);
    }

    /**
     * @covers ::moveFile
     */
    public function test_can_move_a_file()
    {
        $this->createAndSaveFiles(['test.jpg']);

        $response = $this->mediaManager->createDirectory('test');
        $this->assertTrue($response);

        $response = $this->mediaManager->moveFile('test.jpg', '/test/test.jpg');
        $this->assertTrue($response);
    }

    /**
     * @covers ::moveFile
     */
    public function test_can_not_move_a_file_if_destination_file_already_exists()
    {
        $this->createAndSaveFiles(['test.jpg']);

        $response = $this->mediaManager->createDirectory('test');
        $this->assertTrue($response);

        $response = $this->mediaManager->moveFile('test.jpg', 'test.jpg');
        $this->assertFalse($response);

        $errors = $this->mediaManager->errors();
        $this->assertContains('File already exists.', $errors);
    }

    /**
     * @covers ::moveFolder
     */
    public function test_can_that_a_folder_can_be_moved()
    {
        $response1 = $this->mediaManager->createDirectory('/test');
        $response2 = $this->mediaManager->createDirectory('/test/subfolder');
        $this->assertTrue($response1);
        $this->assertTrue($response2);

        $response = $this->mediaManager->moveFolder('/test/subfolder', '/subfolder');
        $this->assertTrue($response);
        $this->assertEquals(true, File::exists(storage_path().'/app/subfolder'));
    }

    /**
     * @covers ::moveFolder
     */
    public function test_can_that_a_folder_cannot_be_moved_inside_itself()
    {
        $response1 = $this->mediaManager->createDirectory('/test');
        $response2 = $this->mediaManager->createDirectory('/test/subfolder');
        $this->assertTrue($response1);
        $this->assertTrue($response2);

        $response1 = $this->mediaManager->moveFolder('/test/subfolder', '/test/subfolder');
        $response2 = $this->mediaManager->moveFolder('/test/subfolder', '/test/subfolder/test');

        $this->assertFalse($response1);
        $this->assertFalse($response2);

        $errors = $this->mediaManager->errors();
        $this->assertContains('Please select another folder to move this folder into.', $errors);
        $this->assertContains('You can not move this folder inside of itself.', $errors);
    }

    /**
     * @covers ::fileRelativePath
     */
    public function test_that_spaces_in_file_names_have_spaces_converted()
    {
        $count = $this->createAndSaveFiles(['example with spaces 1.jpg']);
        $this->assertEquals(1, $count);

        $response = $this->mediaManager->folderInfo();
        $this->assertEquals(
            '/storage/example%20with%20spaces%201.jpg',
            $response['files'][0]['relativePath']
        );

        $this->assertEquals(
            '/storage/example with spaces 1.jpg',
            $response['files'][0]['webPath']
        );
    }

    /**
     * @covers ::__construct
     * @covers ::cleanFolder
     * @covers ::fileDetails
     * @covers ::fileWebpath
     * @covers ::fileMimeType
     * @covers ::fileSize
     * @covers ::fileModified
     * @covers ::fileRelativePath
     */
    public function test_uploaded_files_have_details()
    {
        $count = $this->createAndSaveFiles(['test.jpg']);
        $this->assertEquals(1, $count);

        $response = $this->mediaManager->folderInfo();

        $this->assertDataContains([
            'name'         => 'test.jpg',
            'fullPath'     => '/test.jpg',
            'webPath'      => '/storage/test.jpg',
            'mimeType'     => 'image/jpeg',
            'size'         => 664998,
            'relativePath' => '/storage/test.jpg',
        ], $response['files'][0]);
    }

    /**
     * @covers ::allDirectories
     */
    public function test_a_formatted_list_of_directories_is_generated()
    {
        $this->mediaManager->createDirectory('/test');
        $this->mediaManager->createDirectory('/test/inside');
        $this->mediaManager->createDirectory('/test2');

        $response = $this->mediaManager->allDirectories();

        $this->assertEquals($response, collect([
            '/'            => 'Root',
            '/test'        => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;test',
            '/test/inside' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;inside',
            '/test2'       => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;test2',
        ]));
    }
}
