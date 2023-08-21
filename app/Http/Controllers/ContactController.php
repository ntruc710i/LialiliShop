<?php

namespace App\Http\Controllers;



use App\Mail\ContactedMail;
use App\Models\Contact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{

    /**
     * @OA\Post(
     *      path="/contact/contactAdminTeam",
     *      operationId="contactAdminTeam",
     *      tags={"Contact"},
     *      summary="Contact Admin Team",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Name",
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
     *          name="phone",
     *          in="query",
     *          description="Phone",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="message",
     *          in="query",
     *          description="Message",
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
    /* Send Contact Message To Admin Team */
    public function contactAdminTeam(Request $request){
        $validated = $this->contactValidation($request);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
        ]);

        if($contact){
            Mail::to($contact->email)->queue(new ContactedMail($contact));
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fails']);
    }

    /** Check Contact Form Validation */
    protected function contactValidation($request){
        $validated = $request->validate([
            'name' => 'required | min:5',
            'email' => 'required | email',
            'phone' => 'required',
            'message' => 'required'
        ]);

        return $validated;
    }
}
