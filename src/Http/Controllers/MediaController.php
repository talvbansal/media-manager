<?php
/**
 * Created by PhpStorm.
 * User: talv
 * Date: 10/08/16
 * Time: 12:23.
 */
namespace TalvBansal\MediaManager\Http\Controllers;

use Illuminate\Http\Request;
use TalvBansal\MediaManager\Http\Requests\UploadFileRequest;
use TalvBansal\MediaManager\Http\Requests\UploadNewFolderRequest;
use TalvBansal\MediaManager\Services\MediaManager;

/**
 * Class FileManagerController.
 */
class MediaController extends Controller
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * FileManagerController constructor.
     *
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    public function index()
    {
        return view('media-manager::media.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ls()
    {
        $path = request('path');

        return $this->mediaManager->folderInfo($path);
    }

    /**
     * @param UploadNewFolderRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFolder(UploadNewFolderRequest $request)
    {
        $new_folder = $request->get('new_folder');
        $folder = $request->get('folder').'/'.$new_folder;

        try {
            $result = $this->mediaManager->createDirectory($folder);

            if ($result !== true) {
                $error = $result ?: trans('easel::messages.create_error', ['entity' => 'directory']);

                return $this->errorResponse($error);
            }

            return ['success' => trans('easel::messages.create_success', ['entity' => 'folder'])];
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete a folder.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFolder(Request $request)
    {
        $del_folder = $request->get('del_folder');
        $folder = str_finish($request->get('folder'), DIRECTORY_SEPARATOR).$del_folder;

        try {
            $result = $this->mediaManager->deleteDirectory($folder);
            if ($result !== true) {
                $error = $result ?: trans('easel::messages.delete_error', ['entity' => 'folder']);

                return $this->errorResponse($error);
            }

            return ['success' => trans('easel::messages.delete_success', ['entity' => 'folder'])];
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile()
    {
        $path = request('path');
        try {
            $result = $this->mediaManager->deleteFile($path);

            if ($result !== true) {
                $error = $result ?: trans('easel::messages.delete_error', ['entity' => 'File']);

                return $this->errorResponse($error);
            }

            return ['success' => trans('easel::messages.delete_success', ['entity' => 'File'])];
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Upload new file.
     *
     * @param UploadFileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFiles(UploadFileRequest $request)
    {
        try {
            $files = $request->file('files');
            $folder = $request->get('folder');

            $response = $this->mediaManager->saveFiles($files, $folder);
            $errors = $this->mediaManager->errors();
            $response = trans('easel::messages.upload_success', ['entity' => $response.' New '.str_plural('File', $response)]);

            if (!empty($errors)) {
                return $this->errorResponse($errors, [$response]);
            }

            return ['success' => $response];
        } catch (\Exception $e) {
            return $this->errorResponse([$e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function rename(Request $request)
    {
        $path = $request->get('path');
        $original = $request->get('original');
        $newName = $request->get('newName');
        $type = $request->get('type');

        try {
            $result = $this->mediaManager->rename($path, $original, $newName);

            if ($result !== true) {
                $error = $result ?: trans('easel::messages.rename_error', ['entity' => $type]);

                return $this->errorResponse($error);
            }

            return ['success' => trans('easel::messages.rename_success', ['entity' => $type])];
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function move(Request $request)
    {
        $path = $request->get('path');
        $currentFileName = $request->get('currentItem');
        $newPath = $request->get('newPath');
        $type = $request->get('type');

        $currentFile = str_finish($path, DIRECTORY_SEPARATOR).$currentFileName;
        $newFile = str_finish($newPath, DIRECTORY_SEPARATOR).$currentFileName;

        try {
            $result = $this->mediaManager->move($currentFile, $newFile, ($type == 'Folder'));

            if ($result !== true) {
                $error = $result ?: trans('easel::messages.move_error', ['entity' => $type]);

                return $this->errorResponse($error);
            }

            return ['success' => trans('easel::messages.move_success', ['entity' => $type])];
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function allDirectories()
    {
        return $this->mediaManager->allDirectories();
    }

    /**
     * Upload multiple files.
     *
     * @param       $error
     * @param array $notices
     * @param int   $errorCode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function errorResponse($error, $notices = [], $errorCode = 400)
    {
        if (is_array($error)) {
            json_encode($error);
        }
        $payload = ['error' => $error];
        if (!empty($notices)) {
            $payload['notices'] = $notices;
        }

        return \Response::json($payload, $errorCode);
    }
}
