<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *      path="/user/getProfileData",
     *      operationId="getProfileData",
     *      tags={"CustomerProfile"},
     *      summary="Get Customer Profile",
     *      security={{"sanctum":{}}}, 
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Product")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    // Get My Profile Data
    public function getProfileData() {
        $data = Auth::user();
        return response()->json(['user' => $data]);
    }
}
