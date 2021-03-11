<?php

Route::post('login', 'Api\V1\Admin\UsersApiController@login');
Route::post('register', 'Api\V1\Admin\UsersApiController@register');

Route::group(['prefix' => 'v1', 'name' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');
    Route::get('getUserById/{id}', 'UsersApiController@getById');

    // Product Categories
    Route::post('product-categories/media', 'ProductCategoryApiController@storeMedia')->name('product-categories.storeMedia');
    Route::apiResource('product-categories', 'ProductCategoryApiController');

    // Product Tags
    Route::apiResource('product-tags', 'ProductTagApiController');

    // Products
    Route::post('products/media', 'ProductApiController@storeMedia')->name('products.storeMedia');
    Route::apiResource('products', 'ProductApiController');
    Route::post('fetchNearbyProducts', 'ProductApiController@fetchNearbyProducts');
    Route::post('fetchProductByTopic', 'ProductApiController@fetchProductByTopic');
    Route::post('fetchNearbyProductsWithDate', 'ProductApiController@fetchNearbyProductsWithDate');
    
    // Voting System
    Route::post('upvoteProduct', 'ProductApiController@upvoteProduct');
    Route::post('vote', 'ProductApiController@vote');
    // Sub Categories
    Route::apiResource('sub-categories', 'SubCategoriesApiController');
    Route::get('sub-categories/getById/{category_id}', 'SubCategoriesController@getById')->name('admin.subcategories.byCatId');
    Route::get('getProductCategoriesWithSub', 'ProductCategoryApiController@getProductCategoriesWithSub');
    
    // Locations
    Route::apiResource('locations', 'LocationsApiController');
    Route::get('getAlllocations', 'LocationsApiController@getAll');

});
