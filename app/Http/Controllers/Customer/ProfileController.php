<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
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
     *          @OA\JsonContent(ref="#/components/schemas/User")
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

        /** @OA\Post(
        *     path="/user/updateProfile",
        *     tags={"CustomerProfile"},
        *     summary="Update Customer Profile",
        *     operationId="updateProfile",
        *     security={{"sanctum":{}}},
        *     @OA\RequestBody(
        *          required=true,
        *          @OA\MediaType(
        *              mediaType="multipart/form-data",
        *              @OA\Schema(
        *              type="object",
        *              @OA\Property(property="name", type="string"),
        *              @OA\Property(property="address", type="string"),
        *              @OA\Property(property="phone", type="string"),
        *              @OA\Property(property="avatar", type="string", format="binary"),
        *          )
        *        )
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="successful operation",
        *         @OA\JsonContent(ref="#/components/schemas/Product"),
        *     ),
        *     @OA\Response(
        *          response=401,
        *          description="Unauthenticated"
        *     ),
        *     @OA\Response(
        *         response=400,
        *         description="Invalid id supplied",
        *         @OA\JsonContent(
        *              type="object",
        *              @OA\Property(
        *                  property="message",
        *                  type="string",
        *                  example="The specified data is invalid."
        *              ),
        *              @OA\Property(
        *                  property="errors",
        *                  type="object",
        *                  example={
        *                      "name": "The name field is required.",
        *                  },
        *              ),
        *         ),
        *     ),
        * )
        */
    /* Update proflie data*/
    public function updateProfile(Request $request) {
        $user = Auth::user();
        $data = [
            'name'=> $request->name,
            'address'=> $request->address,
            'phone'=> $request->phone,
        ];
        if($request->hasFile('avatar')){
            $data['avatar'] = cloudinary()->upload($request->file('avatar')->getRealPath())->getSecurePath();
        }
        $update=User::where('id',$user->id)->update($data);
        $ss = User::where('id',$user->id)->get();
        return response()->json(['user' => $ss], 200);
    }
}
