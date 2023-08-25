<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ResetPasswordController extends Controller
{

    /**
     * @OA\Post(
     *      path="/account/emailVerification",
     *      operationId="emailVerification",
     *      tags={"ResetPassword"},
     *      summary="Email Verification",    
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Email",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    // Verify Email And Create A Token
    public function emailVerification(Request $request){
        $validated = $request->validate([
            'email' => 'required | email'
        ]);

        DB::table('password_resets')->updateOrInsert([
                'email' => $validated['email'],
            ],
            [
                'token' => Str::random(43),
                'created_at' => Carbon::now()
            ]
        );

        $token = DB::table('password_resets')
                    ->where('email', $validated['email'])
                    ->value('token');

        Mail::to($validated['email'])->queue(new VerificationMail($token));

        return response()->json(['token' => $token]);
    }

    /**
     * @OA\Post(
     *      path="/account/resetPassword",
     *      operationId="resetPassword",
     *      tags={"ResetPassword"},
     *      summary="Reset Password",    
     *      @OA\Parameter(
     *          name="token",
     *          in="query",
     *          description="token",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Email",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          in="query",
     *          description="Pasword",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          in="query",
     *          description="password_confirmation",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    /* Reset Password */
    public function resetPassword(Request $request){
        $validated = $request->validate([
            'token' => 'required | string',
            'email' => 'required | email | exists:users',
            'password' => 'required | string | confirmed',
            'password_confirmation' => 'required'
        ]);

        $passwordResetUser = DB::table('password_resets')
                                ->where([
                                    'email' => $validated['email'],
                                    'token' => $validated['token']
                                ])
                                ->first();

        if(!$passwordResetUser){
            return response()->json(['status' => 'fails']);
        }

        User::whereEmail($validated['email'])
            ->update([
                'password' => Hash::make($validated['password'])
            ]);

        DB::table('password_resets')
            ->where('email', $validated['email'])
            ->delete();

        return response()->json(['status' => 'success']);
    }
}
