<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ProviderLoginController;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController as AdContactControler;

use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\Customer\ItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/* Authentication Section */
Route::controller(AuthController::class)->prefix('auth')->group(function(){
    Route::post('/admin/register', 'adminRegister')->middleware(['auth:sanctum', 'isAdmin']);
    Route::post('/register', 'customerRegister');
    Route::post('/login', 'login');

    Route::get('/logout', 'logout')->middleware('auth:sanctum');

});

/* Provider Login Section */
Route::controller(ProviderLoginController::class)->prefix('auth')->group(function(){
    Route::get('/{provider}/redirect', 'providerLoginRedirect');
    Route::get('/{provider}/callback', 'providerLoginCallback');
    Route::get('/provider/login/{email}', 'providerLogin');
});

/** For Admin Back Office */
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {

     /* Category Section */
     Route::controller(CategoryController::class)->prefix('category')->group(function(){
        Route::post('/createCategory', 'createCategory');
        Route::get('/getAllCategories', 'getAllCategories');
        Route::delete('/deleteCategory/{id}', 'deleteCategory');
        Route::post('/updateCategory/{id}', 'updateCategory');
        Route::get('/takeDataToEdit/{id}', 'takeDataToEdit');
        Route::get('/takeCategories', 'takeCategories');
        Route::get('/getAllCategories/{searchKey}', 'getAllCategories');
    });

    /* Product Section */
    Route::controller(ProductController::class)->prefix('product')->group(function(){
        Route::get('/getAllProducts', 'getAllProducts');
        Route::post('/createProduct', 'createProduct');
        Route::delete('/deleteProduct/{id}', 'deleteProduct');
        Route::get('/getProductData/{id}', 'getProductData');
        Route::post('/updateProduct', 'updateProduct');
        Route::get('/getAllProducts/{searchKey}', 'getAllProducts');
        Route::get('/filterProducts/{id}', 'filterProductsByCategory');

        /* Product Attribute */
        Route::get('/getProductAttribute/{id}', 'getProductAttribute');
        Route::post('/addProductAttribute', 'addProductAttribute');
        Route::post('/updateProductAttribute','updateProductAttribute');
        Route::delete('/deleteProductAttribute/{id}', 'deleteProductAttribute');
        
        /* Product Images */
        Route::get('/getProductImage/{id}', 'getProductImage');
        Route::delete('/deleteProductImage/{id}', 'deleteProductImage');
    });

    

    /* User Section */
    Route::controller(UserController::class)->prefix('user')->group(function(){
        Route::get('/getAllAdmins', 'getAllAdmins');
        Route::get('/getAllCustomers', 'getAllCustomers');
        Route::get('/getMyProfile/{id}', 'getMyProfile');
        Route::delete('/deleteUser/{id}', 'deleteUser');
        Route::post('/updateUser/{id}', 'updateUser');
        Route::post('/changeRole', 'changeRole');
        Route::post('/changePassword', 'changePassword');
        Route::get('/getAllCustomers/{searchKey}', 'getAllCustomers');
    });

    /* Category Section */
    Route::controller(OrderController::class)->prefix('order')->group(function(){
        Route::get('/getAllOrders', 'getAllOrders');
        Route::get('/getAllOrders/{searchKey}', 'getAllOrders');
        Route::get('/getOrderDetail/{orderCode}', 'getOrderDetail');
        Route::put('/updateStatusOrder/{id}', 'updateStatusOrder');
        Route::delete('/deleteOrder/{id}', 'deleteOrder');
    });

    Route::controller(AdContactControler::class)->prefix('contact')->group(function(){
        Route::get('/getAllContacts', 'getAllContacts');
        Route::get('/getAllContacts/{searchKey}', 'getAllContacts');
        Route::get('/getContactDetail/{id}', 'getContactDetail');
        Route::put('/readContact/{id}', 'readContact');
        Route::put('/replyContact/{id}', 'replyContact');
        Route::delete('/deleteContact/{id}', 'deleteContact');
    });

});


/** For Customer Shopping App */

/** Items Section */
Route::controller(ItemController::class)->prefix('item')->group(function(){
    Route::get('/getAllItems', 'getAllItems');
    Route::get('/getSearchItems/{searchKey}', 'getAllItems');
    Route::get('/getAllCategories', 'getAllCategories');
    Route::get('/filterItemsByCategory/{id}', 'filterItemsByCategory');
    Route::get('/getLatestItems', 'getLatestItems');
    Route::get('/getPopularItems', 'getPopularItems');
    Route::get('/getBestRatingItems', 'getBestRatingItems');
    Route::get('/getItem/{id}', 'getItem');
    Route::get('/getProductImage/{product_id}', 'getProductImage');
    Route::get('/getProductAttribute/{product_id}', 'getProductAttribute');

    Route::get('/filterItems/{id}', 'filterItems');
});

/** Forgot Password, Email Verification, Reset Password */
Route::controller(ResetPasswordController::class)->prefix('account')->group(function(){
    Route::post('emailVerification', 'emailVerification');
    Route::post('resetPassword',  'resetPassword');
});

/** For Auth Customer */
Route::middleware(['auth:sanctum', 'isCustomer'])->group(function(){

    /** Customer Profile Section */
    Route::controller(ProfileController::class)->prefix('user')->group(function(){
        Route::get('/getProfileData', 'getProfileData');
        Route::post('/updateProfile', 'updateProfile');
    });

    /** Customer Shopping Section */
    Route::controller(ShopController::class)->prefix('shop')->group(function(){
        Route::post('/addItemsToCart', 'addItemsToCart');
        Route::get('/getAllCartItems', 'getAllCartItems');
        Route::put('/updateCartItemQuantity', 'updateCartItemQuantity');
        Route::delete('/deleteCartItem/{id}', 'deleteCartItem');
        Route::post('/orderCheckout', 'orderCheckout');
        Route::get('/getAllOrders', 'getAllOrders');
        Route::get('/getOrderDetail/{orderCode}', 'getOrderDetail');
        Route::post('/buyNow', 'buyNow');
    });

    /** Contact To Admin Team Section */
    Route::controller(ContactController::class)->prefix('contact')->group(function(){
        Route::post('/contactAdminTeam', 'contactAdminTeam');
    });
});




