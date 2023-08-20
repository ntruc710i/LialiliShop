<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/auth/admin/register",
     *     tags={"Auth"},
     *     summary="Register Admin into system",
     *     description="Returns a info auth.",
     *     operationId="registerAdmin",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The user name for login",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The Email for register",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="The phone number for register",
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
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The phone number for register",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         ),
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
     *                      "email": "The email field is required.",
     *                  },
     *              ),
     *         ),
     *     ),
     * )
     */
    /* Register New Admin Account */
    public function adminRegister(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:15',
            'email' => 'required|string|unique:users,email',
            'phone' => 'required|string',
            'role' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $this->checkEmailExist($validated['email']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password'])
        ]);

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }



    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Auth"},
     *     summary="Register user customer into system",
     *     description="Returns a info auth.",
     *     operationId="registerUser",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The name for register",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email for register",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
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
    /* Register New Customer Account */
    public function customerRegister(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:15',
            'email' => 'required|email|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $this->checkEmailExist($validated['email']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'customer',
            'password' => Hash::make($validated['password'])
        ]);

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Logs user into system",
     *     description="Returns a info auth.",
     *     operationId="loginUser",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The user name for login",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *         
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
     *                      "email": "The email field is required.",
     *                  },
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Unauthenticated."
     *              ),
     *         ),
     *     )
     * )
     */
    /* User Login */
    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'role' => 'required'
        ]);

        $user = User::where("email", $validated['email'])->first();

        if(!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json([
                "message" => "The credentials does not match."
            ]);
        }

        if($user->role !== $validated['role']){
            return response()->json([
                "message" => "You are not ".$user->role
            ]);
        }

        $user["token"] = $user->createToken(time())->plainTextToken;

        return response()->json([
            'user' => $user,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logs user into system",
     *     description="Returns a info auth.",
     *     operationId="logoutUser",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="successful operation", 
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
     *                      "email": "The email field is required.",
     *                  },
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Unauthenticated."
     *              ),
     *         ),
     *     )
     * )
     */
    /* User Logout */
    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    /* Check Email Already Exist Or Not */
    protected function checkEmailExist($email){
        $old_user = User::where('email', $email)->first();

        if($old_user){
            return response()->json([
                "message" => "This email has already been taken."
            ]);
        }
    }
}
