<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
     * @license Apache 2.0
     */

    /**
     * @OA\Info(
     *     description="This is a sample Userstore server.  You can find out more about Swagger at [http://swagger.io](http://swagger.io) or on [irc.freenode.net, #swagger](http://swagger.io/irc/).",
     *     version="1.0.0",
     *     title="API Documentation",
     *     termsOfService="http://swagger.io/terms/",
     *     @OA\Contact(
     *         email="apiteam@swagger.io"
     *     ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     *  @OA\Server(
     *      url="https://lialili.fly.dev/api/",
     *      description="Development Environment"
     *  )
     *
     *  @OA\Server(
     *      url="http://127.0.0.1:8000/api/",
     *      description="Staging  Environment"
     * )
     * @OA\ExternalDocumentation(
     *     description="Find out more about Swagger",
     *     url="http://swagger.io"
     * )
     */
class Controller extends BaseController
{
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
