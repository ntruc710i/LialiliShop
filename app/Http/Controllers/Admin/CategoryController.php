<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * @OA\Post(
     *      path="/admin/category/createCategory",
     *      operationId="createCategory",
     *      tags={"Categories"},
     *      summary="create Category",
     *      security={{"sanctum":{}}}, 
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          description="create Category",
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
    /* Create New Category */
    public function createCategory(Request $request){
        $newCategory = Category::create(['title' => $request->title]);
        $data = Category::where('id', $newCategory->id)->first();
        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *      path="/admin/category/getAllCategories",
     *      operationId="adgetAllCategories",
     *      tags={"Categories"},
     *      summary="get All Categories",
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
    /* Get All Categories */
    public function getAllCategories(){
        $categories = Category::when(request('searchKey'), function($query){
                                    $query->orWhere('title', 'like', '%'.request('searchKey').'%');
                                })
                                ->get();
        return response()->json($categories, 200);
    }

    /**
     * @OA\Get(
     *      path="/admin/category/takeCategories",
     *      operationId="takeCategories",
     *      tags={"Categories"},
     *      security={{"sanctum":{}}},  
     *      summary="take Categories", 
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
    public function takeCategories(){
        $categories = Category::select('id', 'title')->get();
        return response()->json($categories, 200);
    }


    /**
     * @OA\Delete(
     *      path="/admin/category/deleteCategory/{id}",
     *      operationId="deleteCategory",
     *      tags={"Categories"},
     *      summary="Delete Category",
     *      security={{"sanctum":{}}},       
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category ID",
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
    /* Delete Category */
    public function deleteCategory($id){
        Category::where('id', $id)->delete();
        return response()->json([
            'status' => 'delete success'
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/admin/category/takeDataToEdit/{id}",
     *      operationId="takeDataToEdit",
     *      tags={"Categories"},
     *      summary="take Data To Edit",
     *      security={{"sanctum":{}}}, 
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Category ID",
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
    /* Take Category To Edit */
    public function takeDataToEdit($id){
        $category = Category::where('id', $id)->first();
        return response()->json($category, 200);
    }

    /**
     * @OA\Post(
     *      path="/admin/category/updateCategory/{id}",
     *      operationId="updateCategory",
     *      tags={"Categories"},
     *      summary="Update Category",
     *      security={{"sanctum":{}}}, 
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Update Category",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          description="Update Category",
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
    /* Update Category */
    public function updateCategory($id, Request $request){
        Category::where('id', $id)->update([
            'title' => $request->title
        ]);

        $data = Category::where('id', $id)->first();
        return response()->json($data, 200);
    }
}
