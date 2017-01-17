<?php
/**
 * Created by PhpStorm.
 * User: talv
 * Date: 02/10/16
 * Time: 18:59.
 */

namespace TalvBansal\MediaManager\Http;

use Illuminate\Support\Collection;
use TalvBansal\MediaManager\Contracts\UploadedFilesInterface;

class UploadedFiles extends Collection implements UploadedFilesInterface
{
    /**
     * @return Collection
     */
    public function getUploadedFiles()
    {
        return $this;
    }
}
