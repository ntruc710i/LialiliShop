<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    /**
     * @OA\Get(
     *      path="/admin/contact/getAllContacts",
     *      operationId="adgetAllContacts",
     *      tags={"Contacts"},
     *      security={{"sanctum":{}}},
     *      summary="Get All Contacts",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Contact")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     
     */
    /* Get All Orders */
    public function getAllContacts(){
        $data = Contact::when(request("searchKey"), function($query){
                                $query->orWhere('phone', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('email', 'like', '%'.request('searchKey').'%');
                            })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['contacts' => $data]);
    }

    /**
     * @OA\Get(
     *      path="/admin/contact/getContactDetail/{id}",
     *      operationId="getContactDetail",
     *      tags={"Contacts"},
     *      summary="get Contacts Detail",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="id contact",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Contact")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function getContactDetail($id){
        $data = Contact::where('id', $id)->get();

        return response()->json(['contacts' => $data]);
    }

    /**
     * @OA\Put(
     *      path="/admin/contact/readContact/{id}",
     *      operationId="readContact",
     *      tags={"Contacts"},
     *      summary="read Status Contact",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Contact ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function readContact($id){
        $order=Contact::where('id', $id)->first();
        $read=$order->read;
        if($read == 0){
            $read=1;
        }else if($read == 1){
            $read=0;
        }

        Contact::where('id', $id)->update(['read' => $read]);
        $data = Contact::where('id', $id)->get();

        return response()->json(['contacts' => $data]);
    }

    /**
     * @OA\Put(
     *      path="/admin/contact/replyContact/{id}",
     *      operationId="replyContact",
     *      tags={"Contacts"},
     *      summary="reply Status Contact",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Contact ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function replyContact($id){
        $contact=Contact::where('id', $id)->first();
        $reply=$contact->reply;
        if($reply == 0){
            $reply=1;
        }else if($reply == 1){
            $reply=0;
        }

        Contact::where('id', $id)->update(['reply' => $reply]);
        $data = Contact::where('id', $id)->get();

        return response()->json(['contacts' => $data]);
    }


    /**
     * @OA\Delete(
     *      path="/admin/contact/deleteContact/{id}",
     *      operationId="deleteContact",
     *      tags={"Contacts"},
     *      summary="Delete Contact",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Contact ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    /* Delete Contact */
    public function deleteContact($id){
        Contact::where('id', $id)->delete();
        return response()->json(['status' => 'delete success'], 200);
    }
}
