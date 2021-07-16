<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
DB::connection('mongodb')->enableQueryLog();

// Login
Route::get('/login', 'LoginController@login');
Route::post('/login-confirm', 'LoginController@loginConfirm');
Route::get('/logout', 'LoginController@logout');
Route::get('/signup', 'LoginController@signup');
Route::post('/signup-submit', 'UserController@createNewUser');
Route::post('/change-password', 'UserController@changePasswordUser');

// Home
Route::get('/', 'UserController@showEditProfileView');
Route::get('/home', 'UserController@showEditProfileView');
Route::post('/save-info', 'UserController@saveUserInfo');

// Other
Route::post('/import', 'DataController@import');
Route::get('/review-data', 'DataController@showReviewDataView');
Route::post('/review-data/delete/{_id}', 'DataController@deleteReviewData');
Route::get('/review-data/submit/{collection}/{random_key}', 'DataController@saveReviewData');
Route::get('/set-permission/{collection}', 'RoleController@authorizeForCollection');
Route::post('/edit-roles-list/{collection}', 'RoleController@updateRole');
Route::post('/role/create', 'RoleController@createRole');
Route::get('/set-permission/role/edit/{_id}', 'RoleController@getDataToEditRole');
Route::post('/set-permission/role/delete/{_id}', 'RoleController@deleteRole');
Route::post('/set-permission/role/save/{_id}', 'RoleController@saveRole');
Route::get('/import2', 'DataController@getPaginatedData');

// Error
Route::get('/permission-error', 'RoleController@showErrorView');

// User
Route::get('/admin/users', 'UserController@showViewUsersAdmin');
Route::post('/admin/user/create', 'UserController@createUserAdmin');
Route::get('/admin/user/edit/{_id}', 'UserController@getDataToEditUserAdmin');
Route::post('/admin/user/save/{_id}', 'UserController@saveUserAdmin');
Route::post('/admin/user/delete/{_id}', 'UserController@deleteUserAdmin');
Route::get('/admin/user/search/', 'UserController@searchUser');
Route::post('/admin/user/delete-all', 'UserController@deleteAllUserAdmin');

// Customer
Route::get('/customers', 'CustomerController@showViewCustomers');
Route::get('/customers/get-data', 'CustomerController@getDataCustomer');
Route::post('/customer/create', 'CustomerController@createCustomer');
Route::get('/customer/edit/{_id}', 'CustomerController@getDataToEditCustomer');
Route::post('/customer/save/{_id}', 'CustomerController@saveCustomer');
Route::post('/customer/delete/{_id}', 'CustomerController@deleteCustomer');
Route::post('/customer/import', 'CustomerController@importCustomer');
Route::post('/customer/export/', 'CustomerController@exportCustomer');
Route::get('/customer/search/', 'CustomerController@searchCustomer');
Route::post('/customer/delete-all', 'CustomerController@deleteAllCustomer');

// Order
Route::get('/orders', 'OrderController@showViewOrders');
Route::get('/orders/get-data', 'OrderController@getDataOrder');
Route::post('/order/delete/{_id}', 'OrderController@deleteOrder');
Route::post('/order/create', 'OrderController@createOrder');
Route::get('/order/edit/{_id}', 'OrderController@getDataToEditOrder');
Route::post('/order/save/{_id}', 'OrderController@saveOrder');
Route::get('/order/products/{_id}', 'OrderController@getProductsListOrder');
Route::post('/order/import', 'OrderController@importOrder');
Route::post('/order/export/', 'OrderController@exportOrder');
Route::get('/order/search/', 'OrderController@searchOrder');
Route::post('/order/delete-all', 'OrderController@deleteAllOrder');

// Company
Route::get('/companies', 'CompanyController@showViewCompanies');
Route::post('/company/delete/{_id}', 'CompanyController@deleteCompany');
Route::post('/company/create', 'CompanyController@createCompany');
Route::get('/company/edit/{_id}', 'CompanyController@getDataToEditCompany');
Route::post('/company/save/{_id}', 'CompanyController@saveCompany');
Route::post('/company/import', 'CompanyController@importCompany');
Route::post('/company/import/review-data', 'CompanyController@reviewDataImportCompany');
Route::post('/company/export/', 'CompanyController@exportCompany');
Route::get('/company/search/', 'CompanyController@searchCompany');
Route::post('/company/delete-all', 'CompanyController@deleteAllCompany');


// Product
Route::get('/products', 'ProductController@showViewProducts');
Route::get('/products/get-data', 'ProductController@getDataProduct');
Route::post('/product/delete/{_id}', 'ProductController@deleteProduct');
Route::post('/product/create', 'ProductController@createProduct');
Route::get('/product/edit/{_id}', 'ProductController@getDataToEditProduct');
Route::post('/product/save/{_id}', 'ProductController@saveProduct');
Route::post('/product/import', 'ProductController@importProduct');
Route::post('/product/export/', 'ProductController@exportProduct');
Route::get('/product/search/', 'ProductController@searchProduct');
Route::post('/product/delete-all', 'ProductController@deleteAllProduct');

// Check error
Route::get('/{any}', function () {
    abort('404');
})->where('any', '.*');
