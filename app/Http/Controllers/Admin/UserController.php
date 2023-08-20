<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/admin/user/getAllAdmins/",
     *      operationId="getAllAdmins",
     *      tags={"Users"},
     *      summary="Get All Admin",
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
    /* Get All Users */
    public function getAllAdmins(){
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();

        for($i = 0; $i < count($admins); $i++){
            $admins[$i]["createdAt"] = $admins[$i]->created_at->diffForHumans();
            $admins[$i]["updatedAt"] = $admins[$i]->updated_at->diffForHumans();
        }

        return response()->json($admins, 200);
    }
    /**
     * @OA\Get(
     *      path="/admin/user/getAllCustomers/",
     *      operationId="getAllCustomers",
     *      tags={"Users"},
     *      summary="Get All Customer",
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

    /* Get All Customers */
    public function getAllCustomers(){
        $customers = User::when(request('searchKey'), function($query){
                                $query->orWhere('name', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('email', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('phone', 'like', '%'.request('searchKey').'%');
                            })
                            ->whereRole('customer')
                            ->orderBy('created_at', 'desc')
                            ->paginate(5);

        // for($i = 0; $i < count($customers["data"]); $i++){
        //     $customers["data"][$i]["createdAt"] = $customers["data"][$i]->created_at->diffForHumans();
        //     $customers["data"][$i]["updatedAt"] = $customers["data"][$i]->updated_at->diffForHumans();
        // }

        return response()->json($customers, 200);
    }

    /**
     * @OA\Get(
     *      path="/admin/user/getMyProfile/{id}",
     *      operationId="getMyProfile",
     *      tags={"Users"},
     *      summary="Get My Profile",
     *      security={{"sanctum":{}}}, 
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="User ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
    /* Get My Profile */
    public function getMyProfile($id){
        $myProfile = User::find($id);

        return response()->json($myProfile, 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/user/updateUser/{id}",
     *     tags={"Users"},
     *     summary="Update User",
     *     operationId="updateUser",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="User ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The user name for upadte",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The Email for upadte",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="The phone number for upadte",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="The role for upadte",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         ),
     *     ),
     * )
     */
    /* Update User Data */
    public function updateUser($id, Request $request){
        $user = $this->requestDataForUser($request);

        User::where("id", $id)->update($user);
        $data = User::find($id);
        return response()->json($data, 200);
    }

    /**
     * @OA\Delete(
     *      path="/admin/user/deleteUser/{id}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Delete User",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="User ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
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
    /* Delete User Account */
    public function deleteUser($id){
        $oldData = User::where("id", $id)->first();
        $dbImage = $oldData->image;

        if($dbImage != null){
            Storage::delete('public/'.$dbImage);
        }

        User::where("id", $id)->delete();
        return response()->json(["status" => "Delete success"], 200);
    }


    /**
     * @OA\Post(
     *      path="/admin/user/changeRole",
     *      operationId="changeRole",
     *      tags={"Users"},
     *      summary="Change User Role",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="User ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="role",
     *          in="query",
     *          description="User Role",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
    /* Change User Role */
    public function changeRole(Request $request){
        User::where("id", $request->id)->update(["role"=> $request->newRole]);
        $user = User::where("id", $request->id)->first();
        $user["createdAt"] = $user->created_at->diffForHumans();
        $user["updatedAt"] = $user->updated_at->diffForHumans();
        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *      path="/admin/user/changePassword",
     *      operationId="changePassword",
     *      tags={"Users"},
     *      summary="Change User Password",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="query",
     *          description="User ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="oldPassword",
     *          in="query",
     *          description="Old Password",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="newPassword",
     *          in="query",
     *          description="New Password",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
    /* Change User Password */
    public function changePassword(Request $request){
        $user = User::where("id", $request->id)->first();

        if(!Hash::check($request->oldPassword, $user->password)){
            return response()->json([
                "message" => "The old password you entered is not correct."
            ]);
        }

        User::where("id", $request->id)->update([
            'password' => Hash::make($request->newPassword)
        ]);

        return response()->json([
            "message" => "success"
        ]);
    }

    /* Request Data For User Data Update */
    private function requestDataForUser($request){
        return [
            "name" => $request->name,
            "email" => $request->email,
            "role" => $request->role,
            "phone" => $request->phone
        ];
    }
}
