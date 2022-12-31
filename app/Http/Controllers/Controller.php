<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\ImageHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *     @OA\Info(
 *         version="1.0",
 *         title="Nova Kudos",
 *         description="Demo Nova Kudos Api",
 *     )
 * @OA\Server(
 *      url="https://sandbox-kudos.novaday.ir/api-api lists",
 *      description="Kudos Api"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponser, ImageHandler;
}
