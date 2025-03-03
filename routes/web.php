<?php

use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TechController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PalletController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;

use App\Http\Controllers\Admin\MenuListController;
use App\Http\Controllers\Admin\UserMenuController;
use App\Http\Controllers\Admin\UserSiteController;
use App\Http\Controllers\Admin\VirtualASController;
use App\Http\Controllers\JobOrderRequiestController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\MyClosePrioController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Admin\AppointmentStatusController;
use App\Http\Controllers\Admin\SliassetmonitoringController;
use App\Http\Controllers\MrfController;
use App\Mail\MailNotify;
use App\Mail\TestMail;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });

Route::middleware('auth')->group(function () {
    Route::get('/api/stats/appointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::patch('/api/users/{user}/change-role', [UserController::class, 'changeRole']);
    Route::put('/api/users/{user}', [UserController::class, 'update']);
    Route::delete('/api/users/{user}', [UserController::class, 'destory']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    Route::get('/api/users/userlist', [UserController::class, 'listuser']);
    Route::patch('/api/users/{user}/change-sitehead', [UserController::class, 'changesitehead']);
    //client
    Route::get('/api/view-clients', [ClientController::class, 'viewclient']);
    Route::get('/api/clients', [ClientController::class, 'index']);
    Route::post('/api/clients', [ClientController::class, 'store']);
    Route::put('/api/clients/{client}', [ClientController::class, 'update']);
    Route::delete('/api/clients/{client}', [ClientController::class, 'destory']);
    Route::delete('/api/clients', [ClientController::class, 'bulkDelete']);

    //appointments
    Route::get('/api/appointment-status', [AppointmentStatusController::class, 'getStatusWithCount']);
    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::post('/api/appointments/create', [AppointmentController::class, 'store']);
    Route::get('/api/appointments/{appointment}/edit', [AppointmentController::class, 'edit']);
    Route::put('/api/appointments/{appointment}/edit', [AppointmentController::class, 'update']);
    Route::delete('/api/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    //setting
    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::post('/api/settings', [SettingController::class, 'update']);

    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::post('/api/upload-profile-image', [ProfileController::class, 'uploadImage']);
    Route::post('/api/change-user-password', [ProfileController::class, 'changePassword']);

    //technical module
    Route::resource('/api/tech-recommendations', TechController::class);
    Route::delete('/api/tech-recommendations', [TechController::class, 'bulkDelete']);
    Route::post('/api/tech-getaction', [TechController::class, 'getAction']);



    //tasks controller
    Route::resource('/web/dailytask', TaskController::class);
    Route::put('/web/dailytask/onhandler/{id}', [TaskController::class, 'onhandler']);
    Route::put('/web/dailytask/onTashHoliday/{id}', [TaskController::class, 'onTashHoliday']);
    Route::post('/web/dailytask/addnewTask', [TaskController::class, 'addTask']);
    Route::get('/web/dailytask/{id}/tasks', [TaskController::class, 'getTask']);
    Route::put('/web/dailytask/drop/{id}', [TaskController::class, 'drop']);
    Route::delete('/web/dailytask/deleteTask/{id}', [TaskController::class, 'deleteTask']);
    Route::get('/web/dailytask/filter-taskdate', [TaskController::class, 'FilterTaskdate']);
    Route::get('/web/getsite', [TaskController::class, 'getSite']);
    //myvsc controller

    Route::post('/api/changethemes', [VirtualASController::class, 'changethemes']);
    Route::get('/api/filter-vsc', [VirtualASController::class, 'vscfilter']);
    Route::resource('/api/myvsc', VirtualASController::class);
    //my close prio
    Route::resource('/api/mycloseprio', MyClosePrioController::class);
    Route::get('/api/filter-closeprio', [MyClosePrioController::class, 'FilterClosePrio']);

    //site name
    Route::resource('/api/site', SiteController::class);

    //menu controller
    Route::resource('/web/menulist', MenuListController::class);
    Route::get('/web/GetParentId', [MenuListController::class, 'GetParentId']);

    Route::resource('/web/menu', MenuController::class);

    //menu username
    Route::get('/web/usermenu/{id}', [UserMenuController::class, 'getUserMenus']);
    Route::post('/web/usermenu', [UserMenuController::class, 'store']);
    Route::get('/web/showusermenu/{id}', [UserMenuController::class, 'showusermenu']);
    Route::get('/web/usermenu/retrieve/{id}', [UserMenuController::class, 'retrieveUserMenu']);


    Route::get('/api/chart', [DashboardStatController::class, 'getChartData']);

    Route::resource('/api/notifications', NotificationController::class);
    Route::put('/api/notifications/{id}/markAsRead', [NotificationController::class, 'markAsRead']);

    Route::resource('/web/asset-monitoring', SliassetmonitoringController::class);
    Route::delete('/web/bulkDeleteAsset', [SliassetmonitoringController::class, 'bulkDelete']);
    Route::get('/web/usersite/{id}', [UserSiteController::class, 'getUserSites']);
    Route::get('/web/asset/bulkprint/{id}', [SliassetmonitoringController::class, 'bulkPrint']);



    Route::get('/web/getsitewithoutuser', [UserSiteController::class, 'getsitewthuserid']);

    Route::post('/web/onSaveupdate', [UserSiteController::class, 'onSaveupdate']);

    Route::resource('/web/pallet', PalletController::class);
    Route::post('/web/bulkCreatePallet', [PalletController::class, 'bulkpallet']);
    Route::delete('/web/bulkDeletePallet', [PalletController::class, 'bulkDelete']);


    Route::get('/web/filter-pallet', [PalletController::class, 'filter']);
    Route::get('/web/filterYear', [PalletController::class, 'filterYear']);
    Route::get('/web/filterMonth', [PalletController::class, 'filterMonth']);

    //job request module
    Route::resource('/web/job-request', JobOrderRequiestController::class);

    //Mrf module
    Route::resource('/web/Mrf-request', MrfController::class);


});

Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');
Route::get('/mrf-view', function () {
        return view('emails.mrf_approved');
     });