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
    public function moveFile($currentFile, $newFile);

    public function moveFolder($currentFolder, $newFolder);
}