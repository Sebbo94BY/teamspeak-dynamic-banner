<?php

use App\Http\Controllers\Administration\FontsController;
use App\Http\Controllers\Administration\PhpInfoController;
use App\Http\Controllers\Administration\RolesController;
use App\Http\Controllers\Administration\SystemStatusController;
use App\Http\Controllers\Administration\UsersController;
use App\Http\Controllers\BannerConfigurationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BannerTemplateController;
use App\Http\Controllers\BannerVariableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setup\Installer\RequirementsController;
use App\Http\Controllers\Setup\Installer\UserController;
use App\Http\Controllers\TemplateController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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

Auth::routes(['register' => false]);

Route::get('/', function () {
    if (User::all()->count() == 0) {
        return Redirect::route('setup.installer.requirements');
    }

    return Redirect::route('dashboard');
});

Route::get('/setup/installer/requirements', [RequirementsController::class, 'show_view'])->name('setup.installer.requirements');

Route::get('/setup/installer/user', [UserController::class, 'show_view'])->name('setup.installer.user');
Route::post('/setup/installer/user/add', [UserController::class, 'create'])->name('setup.installer.user.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/instances', [InstanceController::class, 'overview'])->name('instances')->middleware('permission:view instances');
    Route::get('/instance/add', [InstanceController::class, 'add'])->name('instance.add')->middleware('permission:add instances');
    Route::post('/instance/save', [InstanceController::class, 'save'])->name('instance.save')->middleware('permission:add instances');
    Route::get('/instance/{instance_id}/edit', [InstanceController::class, 'edit'])->name('instance.edit')->middleware('permission:edit instances');
    Route::patch('/instance/{instance_id}/update', [InstanceController::class, 'update'])->name('instance.update')->middleware('permission:edit instances');
    Route::delete('/instance/{instance_id}/delete', [InstanceController::class, 'delete'])->name('instance.delete')->middleware('permission:delete instances');
    Route::post('/instance/{instance_id}/start', [InstanceController::class, 'start'])->name('instance.start')->middleware('permission:start instances');
    Route::post('/instance/{instance_id}/stop', [InstanceController::class, 'stop'])->name('instance.stop')->middleware('permission:stop instances');
    Route::post('/instance/{instance_id}/restart', [InstanceController::class, 'restart'])->name('instance.restart')->middleware('permission:restart instances');

    Route::get('/templates', [TemplateController::class, 'overview'])->name('templates')->middleware('permission:view templates');
    Route::get('/template/add', [TemplateController::class, 'add'])->name('template.add')->middleware('permission:add templates');
    Route::post('/template/save', [TemplateController::class, 'save'])->name('template.save')->middleware('permission:add templates');
    Route::get('/template/{template_id}/edit', [TemplateController::class, 'edit'])->name('template.edit')->middleware('permission:edit templates');
    Route::patch('/template/{template_id}/update', [TemplateController::class, 'update'])->name('template.update')->middleware('permission:edit templates');
    Route::delete('/template/{template_id}/delete', [TemplateController::class, 'delete'])->name('template.delete')->middleware('permission:delete templates');

    Route::get('/banners', [BannerController::class, 'overview'])->name('banners')->middleware('permission:view banners');
    Route::get('/banner/add', [BannerController::class, 'add'])->name('banner.add')->middleware('permission:add banners');
    Route::post('/banner/save', [BannerController::class, 'save'])->name('banner.save')->middleware('permission:add banners');
    Route::get('/banner/{banner_id}/edit', [BannerController::class, 'edit'])->name('banner.edit')->middleware('permission:edit banners');
    Route::patch('/banner/{banner_id}/update', [BannerController::class, 'update'])->name('banner.update')->middleware('permission:edit banners');
    Route::delete('/banner/{banner_id}/delete', [BannerController::class, 'delete'])->name('banner.delete')->middleware('permission:delete banners');

    Route::get('/banner/{banner_id}/variables', [BannerVariableController::class, 'overview'])->name('banner.variables');

    Route::get('/banner/{banner_id}/templates', [BannerTemplateController::class, 'edit'])->name('banner.templates')->middleware('permission:edit banners');
    Route::get('/banner/{banner_id}/template/add', [BannerTemplateController::class, 'add_template'])->name('banner.template.add')->middleware('permission:edit banners');
    Route::post('/banner/template/add', [BannerTemplateController::class, 'add'])->name('banner.add.template')->middleware('permission:edit banners');
    Route::delete('/banner/template/{banner_template_id}/remove', [BannerTemplateController::class, 'remove'])->name('banner.template.remove')->middleware('permission:edit banners');
    Route::patch('/banner/template/{banner_template_id}/disable', [BannerTemplateController::class, 'disable'])->name('banner.template.disable')->middleware('permission:edit banners');
    Route::patch('/banner/template/{banner_template_id}/enable', [BannerTemplateController::class, 'enable'])->name('banner.template.enable')->middleware('permission:edit banners');

    Route::get('/banner/configuration/{banner_template_id}', [BannerConfigurationController::class, 'edit'])->name('banner.template.configuration.edit');
    Route::patch('/banner/configuration/{banner_template_id}/upsert', [BannerConfigurationController::class, 'upsert'])->name('banner.template.configuration.upsert');
    Route::delete('/banner/configuration/{banner_configuration_id}/delete', [BannerConfigurationController::class, 'delete'])->name('banner.template.configuration.delete');

    Route::get('/administration/users', [UsersController::class, 'users'])->name('administration.users')->middleware('permission:view users');
    Route::get('/administration/user/add', [UsersController::class, 'add_user'])->name('administration.user.add')->middleware('permission:add users');
    Route::post('/administration/user/create', [UsersController::class, 'create_user'])->name('administration.user.create')->middleware('permission:add users');
    Route::get('/administration/user/{user_id}/edit', [UsersController::class, 'edit_user'])->name('administration.user.edit')->middleware('permission:edit users');
    Route::patch('/administration/user/{user_id}/update', [UsersController::class, 'update_user'])->name('administration.user.update')->middleware('permission:edit users');
    Route::delete('/administration/user/{user_id}/delete', [UsersController::class, 'delete_user'])->name('administration.user.delete')->middleware('permission:delete users');

    Route::get('/administration/roles', [RolesController::class, 'roles'])->name('administration.roles')->middleware('permission:view roles');

    Route::get('/administration/fonts', [FontsController::class, 'fonts'])->name('administration.fonts')->middleware('permission:view fonts');

    Route::get('/administration/systemstatus', [SystemStatusController::class, 'system_status'])->name('administration.systemstatus')->middleware('permission:view system status');

    Route::get('/administration/phpinfo', [PhpInfoController::class, 'php_info'])->name('administration.phpinfo')->middleware('permission:view php info');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'change_password'])->name('profile.update.password');
});
