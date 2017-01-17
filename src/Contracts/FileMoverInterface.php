<?php
/**
 * Created by PhpStorm.
 * User: talv
 * Date: 02/10/16
 * Time: 18:50.
 */

namespace TalvBansal\MediaManager\Contracts;

interface FileMoverInterface
{
    /**
     * @param   $currentFile
     * @param   $newFile
     *
     * @return bool
     */
    public function moveFile($currentFile, $newFile);

    /**
     * @param $currentFolder
     * @param $newFolder
     *
     * @return mixed
     */
    public function moveFolder($currentFolder, $newFolder);
}
