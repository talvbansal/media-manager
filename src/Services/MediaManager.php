<?php

namespace TalvBansal\MediaManager\Services;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MediaManager
{
    /**
     * @var FilesystemAdapter
     */
    protected $disk;

    /**
     * @var PhpRepository
     */
    protected $mimeDetect;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * UploadsManager constructor.
     *
     * @param PhpRepository $mimeDetect
     */
    public function __construct(PhpRepository $mimeDetect)
    {
        $this->disk = Storage::disk('public');
        $this->mimeDetect = $mimeDetect;
    }

    /**
     * Return files and directories within a folder.
     *
     * @param string $folder
     *
     * @return array of [
     *               'folder' => 'path to current folder',
     *               'folderName' => 'name of just current folder',
     *               'breadCrumbs' => breadcrumb array of [ $path => $foldername ]
     *               'subfolders' => array of [ $path => $foldername] of each subfolder
     *               'files' => array of file details on each file in folder
     *               ]
     */
    public function folderInfo($folder)
    {
        $folder = $this->cleanFolder($folder);
        $breadcrumbs = $this->breadcrumbs($folder);
        $folderName = $breadcrumbs->pop();

        // Get sub folders within a folder
        $subfolders = collect($this->disk->directories($folder))->reduce(function ($subfolders, $subFolder) {
            $subfolders["/$subFolder"] = basename($subFolder);

            return $subfolders;
        }, []);

        // Get all files within a folder
        $files = collect($this->disk->files($folder))->map(function ($path) {
            // Don't show hidden files or folders
            if (!starts_with(last(explode(DIRECTORY_SEPARATOR, $path)), '.')) {
                return $this->fileDetails($path);
            }
        });

        return compact('folder', 'folderName', 'breadcrumbs', 'subfolders', 'files');
    }

    /**
     * Sanitize the folder name.
     *
     * @param $folder
     *
     * @return string
     */
    protected function cleanFolder($folder)
    {
        return '/'.trim(str_replace('..', '', $folder), '/');
    }

    /**
     * Return breadcrumbs to current folder.
     *
     * @param $folder
     *
     * @return Collection
     */
    protected function breadcrumbs($folder)
    {
        $folder = trim($folder, '/');
        $folders = collect(explode('/', $folder));
        $path = '';

        return $folders->reduce(function ($crumbs, $folder) use ($path) {
            $path .= '/'.$folder;
            $crumbs[$path] = $folder;

            return $crumbs;
        }, collect())->prepend('Root', '/');
    }

    /**
     * Return an array of file details for a file.
     *
     * @param $path
     *
     * @return array
     */
    protected function fileDetails($path)
    {
        $path = '/'.ltrim($path, '/');

        return [
            'name'         => basename($path),
            'fullPath'     => $path,
            'webPath'      => $this->fileWebpath($path),
            'mimeType'     => $this->fileMimeType($path),
            'size'         => $this->fileSize($path),
            'modified'     => $this->fileModified($path),
            'relativePath' => $this->fileRelativePath($path),
        ];
    }

    /**
     * Return the full web path to a file.
     *
     * @param $path
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function fileWebpath($path)
    {
        $path = $this->fileRelativePath($path);

        return url($path);
    }

    /**
     * Return the mime type.
     *
     * @param $path
     *
     * @return mixed|null|string
     */
    public function fileMimeType($path)
    {
        return $this->mimeDetect->findType(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Return the file size.
     *
     * @param $path
     *
     * @return int
     */
    public function fileSize($path)
    {
        return $this->disk->size($path);
    }

    /**
     * Return the last modified time.
     *
     * @param $path
     *
     * @return Carbon
     */
    public function fileModified($path)
    {
        return Carbon::createFromTimestamp($this->disk->lastModified($path));
    }

    /**
     * Create a new directory.
     *
     * @param $folder
     *
     * @return bool|string
     */
    public function createDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        if ($this->disk->exists($folder)) {
            return "Folder '$folder' aleady exists.";
        }

        return $this->disk->makeDirectory($folder);
    }

    /**
     * Delete a directory.
     *
     * @param $folder
     *
     * @return bool|string
     */
    public function deleteDirectory($folder)
    {
        $folder = $this->cleanFolder($folder);
        $filesFolders = array_merge($this->disk->directories($folder), $this->disk->files($folder));
        if (!empty($filesFolders)) {
            return 'Directory must be empty to delete it.';
        }

        return $this->disk->deleteDirectory($folder);
    }

    /**
     * Delete a file.
     *
     * @param $path
     *
     * @return bool|string
     */
    public function deleteFile($path)
    {
        $path = $this->cleanFolder($path);
        if (!$this->disk->exists($path)) {
            return 'File does not exist.';
        }

        return $this->disk->delete($path);
    }

    /**
     * Save a file.
     *
     * @param $path
     * @param $content
     *
     * @return bool
     */
    public function saveFile($path, $content)
    {
        $path = $this->cleanFolder($path);
        if ($this->disk->exists($path)) {
            $this->errors[] = 'File '.$path.' already exists.';

            return false;
        }

        return $this->disk->put($path, $content);
    }

    /**
     * @param $path
     * @param $originalFileName
     * @param $newFileName
     *
     * @return bool|string
     */
    public function rename($path, $originalFileName, $newFileName)
    {
        $path = $this->cleanFolder($path);
        $nameName = $path.DIRECTORY_SEPARATOR.$newFileName;
        if ($this->disk->exists($nameName)) {
            return 'File already exists.';
        }

        return $this->disk->getDriver()->rename(($path.DIRECTORY_SEPARATOR.$originalFileName), $nameName);
    }

    /**
     * Show all directories that the selected item can be moved to.
     *
     * @return array
     */
    public function allDirectories()
    {
        $directories = $this->disk->allDirectories('/');

        return collect($directories)->map(function ($directory) {
            return DIRECTORY_SEPARATOR.$directory;
        })->reduce(function ($allDirectories, $directory) {
            $parts = explode('/', $directory);
            $name = str_repeat('&nbsp;', (count($parts)) * 4).basename($directory);

            $allDirectories[$directory] = $name;

            return $allDirectories;
        }, collect())->prepend('Root', '/');
    }

    /**
     * @param      $currentFile
     * @param      $newFile
     * @param bool $isFolder
     *
     * @return bool|string
     */
    public function move($currentFile, $newFile, $isFolder = false)
    {
        if ($isFolder) {
            if ($newFile == $currentFile) {
                return 'Please select another folder to move this folder into';
            }

            if (starts_with($newFile, $currentFile)) {
                return 'You can not move this folder inside of itself';
            }
        }

        if ($this->disk->exists($newFile)) {
            return 'File already exists.';
        }

        return $this->disk->getDriver()->rename($currentFile, $newFile);
    }

    public function errors()
    {
        return $this->errors;
    }

    /**
     * @param array  $files
     * @param string $folder
     *
     * @return int
     */
    public function saveFiles(array $files, $folder = '/')
    {
        $uploaded = 0;
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            if (!$file->isValid()) {
                $this->errors[] = trans('media-manager::messages.upload_error', ['entity' => $file->getClientOriginalName()]);

                continue;
            }

            $fileName = $file->getClientOriginalName();
            $path = str_finish($folder, DIRECTORY_SEPARATOR).$fileName;
            $content = file_get_contents($file);
            if ($this->saveFile($path, $content)) {
                $uploaded++;
            }
        }

        return $uploaded;
    }

    /**
     * @param $path
     *
     * @return string
     */
    private function fileRelativePath($path)
    {
        $path = str_replace(' ', '%20', $path);

        return '/storage/'.ltrim($path, '/');
    }
}
