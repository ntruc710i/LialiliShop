<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductAttributes;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderList;
use Carbon\Carbon;

class ItemController extends Controller
{

    /**
     * @OA\Get(
     *      path="/item/getAllItems",
     *      operationId="getAllItems",
     *      tags={"Items"},
     *      summary="Get All Items",
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

    
    // Get All Items

    
    public function getAllItems(){
        $data = Product::with(['category', 'productImages', 'productAttributes'])
                        ->when(request('searchKey'), function($query){
                            $query->orWhere('title', 'like', '%'.request('searchKey').'%')
                                    ->orWhere('price', 'like', '%'.request('searchKey').'%');
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
               
        return response()->json(['items' => $data]);
    }
    
    /**
     * @OA\Get(
     *      path="/item/filterItemsByCategory/{id}",
     *      operationId="filterItemsByCategory",
     *      tags={"Items"},
     *      summary="Filter items ByCategory",
     *      description="Filters items ByCategory.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category ID",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="size",
     *          in="query",
     *          description="Size",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="color",
     *          in="query",
     *          description="Color",
     *          
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Limit",
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
    public function filterItemsByCategory($id,Request $request){
        $limit = $request->input('limit');
        if(!$limit){
            $limit = 12;
        }
        $data = Product::with(['category', 'productImages', 'productAttributes'])
                        ->where('category_id', $id)
                        ->when(request('size'), function($query){
                            $query->whereHas('productAttributes', function ($query) {
                                $query->where('size', request('size'));
                            });
                        })
                        ->when(request('color'), function($query){
                            $query->whereHas('productAttributes', function ($query) {
                                $query->where('color', 'like', '%'.request('color').'%');
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate($limit);

        return response()->json(['items' => $data]);
    }

    /**
     * @OA\Get(
     *      path="/item/getLatestItems",
     *      operationId="getLatestItems",
     *      tags={"Items"},
     *      summary="Get Latest Items",
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
    // Get Latest Items
    public function getLatestItems(){
        $data = Product::orderBy('created_at', 'desc')->take(7)->get();

        return response()->json(['items' => $data]);
    }

    /**
     * @OA\Get(
     *      path="/item/getPopularItems",
     *      operationId="getPopularItems",
     *      tags={"Items"},
     *      summary="Get Popular Items",
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
    // Get Popular Items
    public function getPopularItems(){
        $date = Carbon::now()->subDays(30);

        $data = OrderList::where('created_at', '>=', $date)->get();

        if($data){
            return response()->json(['items' => []]);
        }

        $record = [];

        foreach($data as $order){
            if(array_key_exists($order->product_id, $record)){
                $record[$order->product_id] = $record[$order->product_id] + $order->quantity;
            }else {
                $record[$order->product_id] = $order->quantity;
            }
        }

        arsort($record);

        $product_id_array = array_keys($record);

        $items = Product::whereIn('id', $product_id_array)
                        ->orderByRaw("FIELD(id, " . implode(',', $product_id_array) . ")")
                        ->take(7)
                        ->get();

        return response()->json(['items' => $items]);
    }

    // public function getBestRatingItems(){
    //     $orders = OrderList::with('product')->where('user_id', Auth::user()->id)->get();

    //     $record = [];

    //     return response()->json(['items' => $orders]);
    // }


    /**
     * @OA\Get(
     *      path="/item/getItem/{id}",
     *      operationId="getItem",
     *      tags={"Items"},
     *      summary="get Item",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Product ID",
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
    /* Get Item Detail */
    public function getItem($id){
        $item = Product::with(['category', 'productImages', 'productAttributes'])->where('id', $id)->first();

        return response()->json(['item' => $item]);
    }
    
    /**
     * @OA\Get(
     *      path="/item/getAllCategories",
     *      operationId="getAllCategories",
     *      tags={"Items"},
     *      summary="Get All Categories",
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
    // Get All Categories
    public function getAllCategories(){
        $data = Category::get();

        return response()->json(['categories' => $data]);
    }

    /**
     * @OA\Get(
     *      path="/item/getProductAttribute/{product_id}",
     *      operationId="getProductAttribute",
     *      tags={"Items"},
     *      summary="Get Product Attribute",
     *      @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          description="Product Attribute ID",
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
    public function getProductAttribute($product_id){
        $product_attr = ProductAttributes::select('products.*', 'product_attributes.*')
                                        ->where('product_id', $product_id)->leftJoin('products', 'product_attributes.product_id', 'products.id')
                                        ->orderBy("size", "asc")->get();
        return response()->json($product_attr, 200);
    }

    /**
     * @OA\Get(
     *      path="/item/getProductImage/{product_id}",
     *      operationId="getProductImage",
     *      tags={"Items"},
     *      summary="Get Product Image",
     *      @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          description="Product Image ID",
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
    public function getProductImage($product_id){
        $product_imgs = ProductImages::where('product_id', $product_id)->get();
        return response()->json($product_imgs, 200);
    }
}
