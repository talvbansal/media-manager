<?php
/**
 * Created by PhpStorm.
 * User: talv
 * Date: 30/08/16
 * Time: 22:47.
 */

namespace TalvBansal\MediaManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
