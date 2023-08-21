<?php

namespace App\Http\Controllers\Admin;


use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * @OA\Get(
     *      path="/admin/product/getAllProducts",
     *      operationId="getAllProducts",
     *      tags={"Products"},
     *      security={{"sanctum":{}}},
     *      summary="Get All Products",
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
    /* Get All Products */
    public function getAllProducts(){
        $products = Product::select('products.*', 'categories.title as category_title')
                            ->when(request("searchKey"), function($query){
                                $query->orWhere('products.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('categories.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.price', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.count', 'like', '%'.request('searchKey').'%');
                            })
                            ->leftJoin('categories', 'products.category_id', 'categories.id')
                            ->orderBy("created_at", "desc")
                            ->paginate(4);

        for($i = 0; $i < count($products); $i++){
            $products[$i]["createdAt"] = $products[$i]->created_at->diffForHumans();
            $products[$i]["updatedAt"] = $products[$i]->updated_at->diffForHumans();
        }

        return response()->json($products, 200);
    }

    /* Filter Products By Category */
    public function filterProductsByCategory($id){
        $products = Product::select('products.*', 'categories.title as category_title')
                            ->when(request("searchKey"), function($query){
                                $query->orWhere('products.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('categories.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.price', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.count', 'like', '%'.request('searchKey').'%');
                            })
                            ->leftJoin('categories', 'products.category_id', 'categories.id')
                            ->where('category_id', $id)
                            ->orderBy("created_at", "desc")
                            ->paginate(4);

        for($i = 0; $i < count($products); $i++){
            $products[$i]["createdAt"] = $products[$i]->created_at->diffForHumans();
            $products[$i]["updatedAt"] = $products[$i]->updated_at->diffForHumans();
        }

        return response()->json($products, 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/product/createProduct",
     *     tags={"Products"},
     *     summary="Add a new user to the store",
     *     description="Returns a single new user.",
     *     operationId="createProduct",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *          description= "User object that needs to be added to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="price", type="integer"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *              @OA\Property(
     *                      property="images[]",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                  )
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
    /* Create New Product */
    public function createProduct(Request $request){
        $product = $this->requestDataForProduct($request);

        $file_name = uniqid().$request->file('image')->getClientOriginalName();
        //$request->file('image')->storeAs('public/products', $file_name);
        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
        $product['image'] = $uploadedFileUrl;
        
        $newProduct = Product::create($product);
        
        $uploadedFiles = $request->file('images');
        if($newProduct){
            $nextId = Product::orderBy('id','desc')->first()->id;
            foreach ($uploadedFiles as $file) {
                $uploaded = cloudinary()->upload($file->getRealPath())->getSecurePath();
                //$file->storeAs('public/product_images', $file->getClientOriginalName());
                ProductImages::create([
                    'product_id' => $nextId,
                    'image' => $uploaded,
                ]);
            } 
        }
        
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();

        $data = $allData->where("id", $newProduct->id)->first();

        $data["createdAt"] = $data->created_at->diffForHumans();
        $data["updatedAt"] = $data->updated_at->diffForHumans();

        return response()->json($data, 200);
    }

    /**
     * @OA\Delete(
     *      path="/admin/product/deleteProduct/{id}",
     *      operationId="deleteProduct",
     *      tags={"Products"},
     *      summary="Delete Product",
     *      security={{"sanctum":{}}},       
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
    /* Delete Product */
    public function deleteProduct($id){
        $product = Product::where("id", $id)->first();
        $db_image = $product->image;
        Storage::delete('public/'.$db_image);
        Product::where('id', $id)->delete();
        ProductAttributes::where('product_id', $id)->delete();
        ProductImages::where('product_id', $id)->delete();
        return response()->json(['status' => 'delete success'], 200);
    }

    /* Get Product Data to Update */
    public function getProductData($id){
        $product = Product::where('id', $id)->first();
        return response()->json($product, 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/product/updateProduct",
     *     tags={"Products"},
     *     summary="Update product to the store",
     *     description="Returns a product.",
     *     operationId="updateProduct",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *          description= "User object that needs to be added to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="category_id", type="integer"),
     *              @OA\Property(property="price", type="integer"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="image", type="string", format="binary"),
     *              @OA\Property(
     *                      property="images[]",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                  )
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
    /* Update Product */
    public function updateProduct(Request $request){
        $product = $this->requestDataForProduct($request);

        if($request->hasFile('image')){
            $dbData = Product::where('id', $request->id)->first();
            $dbName = $dbData->image;
            Storage::delete('public/products/'.$dbName);

            $file_name = uniqid().$request->file('image')->getClientOriginalName();
            //$request->file('image')->storeAs('public/products', $file_name);
            //$product["image"] = $file_name;
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath())->getSecurePath();
            $product['image'] = $uploadedFileUrl;
            // return response()->json($request, 200);
        }
        
        
            
            
        $update=Product::where('id', $request->id)->update($product);
        if($request->hasFile('images')){
            /*
            $dbData = ProductImages::where('product_id', $request->id)->get();

            foreach ($dbData as $file) {
                $file = $file->image;
                Storage::delete('public/product_images/'.$file);
            } */
            $uploadedFiles = $request->file('images');
            if($update){
                $nextId = Product::orderBy('id','desc')->first()->id;
                foreach ($uploadedFiles as $file) {
                    $uploadedFileUrl = cloudinary()->upload($file->getRealPath())->getSecurePath();
                    //$file->storeAs('public/product_images', $file->getClientOriginalName());
                    ProductImages::create([
                        'product_id' => $nextId,
                        'image' => $file->getClientOriginalName(),
                    ]);
                } 
            // return response()->json($request, 200);
        }}
        $updatedData = Product::where('id', $request->id)->first();
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();

        $data = $allData->where("id", $updatedData->id)->first();

        $data["createdAt"] = $data->created_at->diffForHumans();
        $data["updatedAt"] = $data->updated_at->diffForHumans();

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *      path="/admin/product/getProductAttribute/{product_id}",
     *      operationId="adgetProductAttribute",
     *      tags={"ProductAttributes"},
     *      summary="Get Product Attribute",
     *      security={{"sanctum":{}}},
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
     * @OA\Post(
     *     path="/admin/product/addProductAttribute",
     *     tags={"ProductAttributes"},
     *     summary="Add product Attribute",
     *     description="Returns a product.",
     *     operationId="addProductAttribute",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *          description= "Product Attribute object that needs to be added to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(property="product_id", type="integer"),
     *              @OA\Property(property="size", type="string"),
     *              @OA\Property(property="color", type="string"),
     *              @OA\Property(property="stock", type="integer"),
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
    public function addProductAttribute(Request $request){
        $oldatt = ProductAttributes::where('product_id', $request->product_id)
                                        ->where('size', $request->size)
                                        ->where('color', $request->color)
                                        ->first();

        if($oldatt){
            $data = ProductAttributes::where('product_id', $request->product_id)
                                        ->where('size', $request->size)
                                        ->where('color', $request->color)
                                        ->first();
            return response()->json(['item' => $data, 'status' => 'fails']);
        }

        ProductAttributes::create([
                            'product_id' => $request->product_id,
                            'size' => $request->size,
                            'color' => $request->color,
                            'stock' => $request->stock
                            ]);

        $data = ProductAttributes::where('product_id', $request->product_id)
                                ->where('size', $request->size)
                                ->where('color', $request->color)
                                ->first();

        if($data){
        return response()->json(['item' => $data, 'status' => 'created']);
        }
        }

        /**
     * @OA\Post(
     *     path="/admin/product/updateProductAttribute",
     *     tags={"ProductAttributes"},
     *     summary="Update product Attribute",
     *     description="Returns a product.",
     *     operationId="updateProductAttribute",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *          description= "Product Attribute object that needs to be update to the store",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              type="object",
     *              @OA\Property(property="product_id", type="integer"),
     *              @OA\Property(property="size", type="string"),
     *              @OA\Property(property="color", type="string"),
     *              @OA\Property(property="stock", type="integer"),
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
    public function updateProductAttribute(Request $request){

        
        $attr = ProductAttributes::where('id', $request->id)->first();

        $product_attr = [
            'product_id' => $attr->product_id,
            'size' => $attr->size,
            'color' => $attr->color,
            'stock' => $request->stock,

        ];
        ProductAttributes::where('id', $request->id)->update($product_attr);
        $updatedData = ProductAttributes::where('id', $request->id)->first();
        $allData = ProductAttributes::select('products.*', 'product_attributes.*')
                                    ->where('product_id', $attr->product_id)->leftJoin('products', 'product_attributes.product_id', 'products.id')
                                    ->orderBy("size", "asc")
                                    ->get();

        $data = $allData->where("id", $updatedData->id)->first();

        $data["createdAt"] = $data->created_at->diffForHumans();
        $data["updatedAt"] = $data->updated_at->diffForHumans();

        return response()->json($data, 200);
        
    }
    /**
     * @OA\Delete(
     *      path="/admin/product/deleteProductAttribute/{id}",
     *      operationId="deleteProductAttribute",
     *      tags={"ProductAttributes"},
     *      summary="Delete Product Attribute",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
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
    public function deleteProductAttribute($id){
        $productattr = ProductAttributes::where("id", $id)->delete();
        return response()->json(['status' => 'delete success'], 200);
        
    }


    /**
     * @OA\Get(
     *      path="/admin/product/getProductImage/{product_id}",
     *      operationId="adgetProductImage",
     *      tags={"ProductImages"},
     *      summary="Get Product Image",
     *      security={{"sanctum":{}}}, 
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
    /* Product Images */
    public function getProductImage($product_id){
        $product_imgs = ProductImages::where('product_id', $product_id)->get();
        return response()->json($product_imgs, 200);
    }

    /**
     * @OA\Delete(
     *      path="/admin/product/deleteProductImage/{id}",
     *      operationId="deleteProductImage",
     *      tags={"ProductImages"},
     *      summary="Delete Product Image",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
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
    public function deleteProductImage($product_id){
        $dbData = ProductImages::where('product_id', $product_id)->get();

            foreach ($dbData as $file) {
                $file = $file->image;
                Storage::delete('public/product_images/'.$file);
            } 
        $productattr = ProductImages::where("id", $product_id)->delete();
        return response()->json(['status' => 'delete success'], 200);
        
    }
    
    /* Request Data For Product Create and Update */
    private function requestDataForProduct($request){
        return [
            'title' => $request->title,
            'category_id' => $request->category_id,
            'slug'=> Str::slug($request->title),
            'price' => $request->price,
            'description' => $request->description,
        ];
    }
}
