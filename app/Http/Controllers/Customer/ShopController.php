<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttributes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderList;
use App\Models\Order;
use Carbon\Carbon;

class ShopController extends Controller
{

    /**
     * @OA\Post(
     *      path="/shop/addItemsToCart",
     *      operationId="addItemsToCart",
     *      tags={"Shop"},
     *      summary="Add item to cart",
     *      security={{"sanctum":{}}}, 
     *      @OA\Parameter(
     *          name="product_attribute_id",
     *          in="query",
     *          description="Product Attribute ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="quantity",
     *          in="query",
     *          description="Quantity",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    // Add One Item To Cart
    public function addItemsToCart(Request $request){
        $oldCartItem = Cart::where('user_id', Auth::user()->id)
                            ->where('product_attribute_id', $request->product_attribute_id)
                            ->first();
        $product = ProductAttributes::where('id',$request->product_attribute_id)->first();
        if($oldCartItem){
            Cart::where('user_id', Auth::user()->id)
                ->where('product_attribute_id', $request->product_attribute_id)
                ->update(['quantity' => $oldCartItem->quantity+$request->quantity]);
            

            $data = Cart::with(['product','productAttributes'])
                ->where('user_id', Auth::user()->id)
                ->where('product_attribute_id', $request->product_attribute_id)
                ->first();

            return response()->json(['item' => $data, 'status' => 'fails']);
        }

        Cart::create([
            'user_id' => Auth::user()->id,
            'product_id' => $product->product_id,
            'product_attribute_id' => $request->product_attribute_id,
            'quantity' => $request->quantity
        ]);

        $data = Cart::with(['product','productAttributes'])
                        ->where('user_id', Auth::user()->id)
                        ->where('product_attribute_id', $request->product_attribute_id)
                        ->first();

        if($data){
            return response()->json(['item' => $data, 'status' => 'created']);
        }
    }

    /**
     * @OA\Get(
     *      path="/shop/getAllCartItems",
     *      operationId="getAllCartItems",
     *      tags={"Shop"},
     *      summary="Get All Cart Items",
     *      security={{"sanctum":{}}}, 
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    // Get All Items In Customer's Cart
    public function getAllCartItems(){
        $data = Cart::with(['product','productAttributes'])->where('user_id', Auth::user()->id)->get();

        return response()->json(['cartItems' => $data]);
    }

    /**
 * @OA\Put(
 *     path="/shop/updateCartItemQuantity",
 *     operationId="updateCartItemQuantity",
 *     tags={"Shop"},
 *     summary="Update cart item quantity",
 *     description="Update the quantity of a specific item in the user's cart.",
 *     security={{"sanctum":{}}}, 
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         description="ID of the cart item to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="quantity",
 *         in="query",
 *         description="New quantity for the cart item",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error"
 *     )
 * )
 */
    // Change Cart Item Quantity
    public function updateCartItemQuantity(Request $request){
        Cart::where('id', $request->id)->update(['quantity' => $request->quantity]);
        $cart = Cart::find($request->id);
        if($cart->quantity === $request->quantity){
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'fail']);
    }

    /**
     * @OA\Delete(
     *      path="/shop/deleteCartItem/{id}",
     *      operationId="deleteCartItem",
     *      tags={"Shop"},
     *      summary="Delete Cart Item",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="CartItem ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    // Delete One Cart Item
    public function deleteCartItem($id){
        Cart::find($id)->delete();
        return response()->json(['message' => 'success']);
    }

    /**
     * @OA\Post(
     *      path="/shop/orderCheckout",
     *      operationId="orderCheckout",
     *      tags={"Shop"},
     *      summary="Order Checkout",
     *      security={{"sanctum":{}}},       
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
     *          name="address",
     *          in="query",
     *          description="Address",
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
    // Order Checkout
    public function orderCheckout(Request $request){
        $cartItems = Cart::with(['product','productAttributes'])->where('user_id', Auth::user()->id)->get();
        
        $orderCode = $cartItems[0]->created_at->format('YmdHi').rand(10, 99);
        $totalPrice = 0;

        foreach($cartItems as $item){
            OrderList::create([
                'user_id' => Auth::user()->id,
                'product_id' => $item->product_id,
                'product_attribute_id' => $item->product_attribute_id,
                'quantity' => $item->quantity,
                'total' => $item->quantity * $item->product->price,
                'order_code' => $orderCode,
            ]);
            $stock = ProductAttributes::where('id',$item->product_attribute_id)->first();
            $newstock = $stock->stock - $item->quantity;
            ProductAttributes::where('id',$item->product_attribute_id)->update(['stock' => $newstock]);
            $totalPrice += $item->quantity * $item->product->price;
        }

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'order_code' => $orderCode,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_price' => $totalPrice,
            'status' => 0
        ]);

        if($order){
            Cart::where('user_id', Auth::user()->id)->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail']);
    }

    /**
     * @OA\Get(
     *      path="/shop/getAllOrders",
     *      operationId="getAllOrders",
     *      tags={"Shop"},
     *      summary="Get All Orders",
     *      security={{"sanctum":{}}}, 
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
                    ->where('user_id', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['orders' => $data]);
    }
    /**
     * @OA\Get(
     *      path="/shop/getOrderDetail/{orderCode}",
     *      operationId="getOrderDetail",
     *      tags={"Shop"},
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
     *          @OA\JsonContent(ref="#/components/schemas/OrderList")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     * )
     */
    public function getOrderDetail($orderCode){
        $data = OrderList::with([ "product" => function($query){
                            $query->select('id', 'title', 'price');
                        }])
                        ->where('order_code', $orderCode)
                        ->get();

        return response()->json(['items' => $data]);
    }
    /**
     * @OA\Post(
     *      path="/shop/buyNow",
     *      operationId="buyNow",
     *      tags={"Shop"},
     *      summary="Buy Now",
     *      security={{"sanctum":{}}},       
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
     *          name="address",
     *          in="query",
     *          description="Address",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="product_id",
     *          in="query",
     *          description="Product ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="product_attribute_id",
     *          in="query",
     *          description="Product Attribute ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="quantity",
     *          in="query",
     *          description="Quantity",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="total",
     *          in="query",
     *          description="Total",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
    public function buyNow(Request $request){
        $validated = $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'product_id' => 'required',
            'product_attribute_id' => 'required',
            'quantity' => 'required',
            'total' => 'required'
        ]);

        $orderCode = Carbon::now()->format('YmdHi').rand(10, 99);

        OrderList::create([
            'user_id' => Auth::user()->id,
            'product_id' => $validated['product_id'],
            'product_attribute_id' => $validated[ 'product_id'],
            'quantity' => $validated['quantity'],
            'total' => $validated['total'],
            'order_code' => $orderCode,
        ]);

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'order_code' => $orderCode,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'total_price' => $validated['total'],
            'status' => 0
        ]);

        if($order){
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail']);
    }
}
