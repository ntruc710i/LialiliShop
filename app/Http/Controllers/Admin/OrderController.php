<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderList;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    /**
     * @OA\Get(
     *      path="/admin/order/getAllOrders",
     *      operationId="adgetAllOrders",
     *      tags={"Orders"},
     *      security={{"sanctum":{}}},
     *      summary="Get All Orders",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )

     */
    /* Get All Orders */
    public function getAllOrders(){
        $data = Order::with(['order_list' => function($query) {
                        $query->select('order_code', 'quantity');
                    }])
                    ->when(request("searchKey"), function($query){
                                $query->orWhere('status', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('order_code', 'like', '%'.request('searchKey').'%');
                            })
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['orders' => $data]);
    }
    /**
     * @OA\Get(
     *      path="/admin/order/getOrderDetail/{orderCode}",
     *      operationId="adgetOrderDetail",
     *      tags={"Orders"},
     *      summary="get Order Detail",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="orderCode",
     *          in="path",
     *          description="order Code",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */

    public function getOrderDetail($orderCode){
        $data = OrderList::with([ "product" => function($query){
                            $query->select('id', 'title','image', 'price');
                        }])
                        ->where('order_code', $orderCode)
                        ->get();

        return response()->json(['items' => $data]);
    }

    /**
     * @OA\Put(
     *      path="/admin/order/updateStatusOrder/{id}",
     *      operationId="updateStatusOrder",
     *      tags={"Orders"},
     *      summary="update Status Order",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Order ID",
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
    public function updateStatusOrder($id){
        $order=Order::where('id', $id)->first();
        $status=$order->status;
        if($status == 0){
            $status=1;
        }else if($status == 1){
            $status=0;
        }

        Order::where('id', $id)->update(['status' => $status]);
        $data = Order::with(['order_list' => function($query) {
                        $query->select('order_code', 'quantity');
                    }])
                    ->orderBy('created_at', 'desc')
                    ->where('id', $id)
                    ->get();

        return response()->json(['items' => $data]);
    }

    /**
     * @OA\Delete(
     *      path="/admin/order/deleteOrder/{id}",
     *      operationId="deleteOrder",
     *      tags={"Orders"},
     *      summary="Delete Order",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Order ID",
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
    /* Delete Order */
    public function deleteOrder($id){
        Order::where('id', $id)->delete();
        return response()->json(['status' => 'delete success'], 200);
    }
    
}
