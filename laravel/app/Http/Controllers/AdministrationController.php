<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\SystemStatusController;
use App\Http\Requests\UserAddRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdministrationController extends Controller
{
    /**
     * Display the users view.
     */
    public function users(): View
    {
        return view('administration.users', ['users' => User::all()]);
    }

    /**
     * Display the user add form.
     */
    public function add_user(): View
    {
        return view('administration.user.add', ['roles' => Role::all()]);
    }

    /**
     * Display the user edit form.
     */
    public function edit_user(Request $request): View|RedirectResponse
    {
        try {
            $user = User::findOrFail($request->user_id);
        } catch (ModelNotFoundException) {
            return redirect()->back()->with([
                'error' => 'user-not-found',
                'message' => 'The user, which you have tried to edit, does not exist.',
            ]);
        }

        return view('administration.user.edit', ['user' => $user, 'roles' => Role::all()]);
    }

    /**
     * Creates a new user model in the database.
     */
    public function create_user(UserAddRequest $request): View|RedirectResponse
    {
        $request->validated();

        $initial_password = Str::random(16);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($initial_password),
        ]);

        if (! $user->save()) {
            return Redirect::route('administration.user.add')->withInput($request->all())->with([
                'error' => 'user-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        foreach ($request->roles as $role_id) {
            $user->assignRole($role_id);
        }

        return Redirect::route('administration.users')->with([
            'success' => 'user-add-successful',
            'message' => "Successfully added the new user. Initial password: $initial_password",
        ]);
    }

    /**
     * Updates a single user.
     */
    public function update_user(UserUpdateRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $user = User::findOrFail($request->user_id);
        } catch (ModelNotFoundException) {
            return redirect()->back()->with([
                'error' => 'user-not-found',
                'message' => 'The user, which you have tried to update, does not exist.',
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if (! $user->save()) {
            return redirect()->back()->with([
                'error' => 'user-update-error',
                'message' => 'Failed to update the user in the database. Please try again.',
            ]);
        }

        $user->syncRoles($request->roles);

        return redirect()->back()->with([
            'success' => 'user-update-successful',
            'message' => 'Successfully updated the user.',
        ]);
    }

    /**
     * Deletes a single user.
     */
    public function delete_user(Request $request): RedirectResponse|View
    {
        try {
            $user = User::findOrFail($request->user_id);
        } catch (ModelNotFoundException) {
            return redirect()->back()->with([
                'error' => 'user-not-found',
                'message' => 'The user, which you have tried to delete, does not exist.',
            ]);
        }

        if (! $user->delete()) {
            return redirect()->back()->with([
                'error' => 'user-delete-error',
                'message' => 'Failed to delete the user from the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.users')->with([
            'success' => 'user-delete-successful',
            'message' => 'Successfully deleted the user.',
        ]);
    }

    /**
     * Display the roles view.
     */
    public function roles(): View
    {
        return view('administration.roles', ['roles' => Role::all()]);
    }

    /**
     * Callback function to only return TTF files.
     */
    public function is_ttf_file($value, $key)
    {
        return preg_match("/\.ttf$/", $value);
    }

    /**
     * Display the fonts view.
     */
    public function fonts(): View
    {
        $installed_ttf_files = array_filter(Storage::disk('public')->files('fonts/'), $this->is_ttf_file(...), ARRAY_FILTER_USE_BOTH);

        return view('administration.fonts', ['fonts' => $installed_ttf_files]);
    }

    /**
     * Display system status information.
     */
    public function system_status(): View
    {
        $system_status_helper = new SystemStatusController();
        $system_status = $system_status_helper->system_status_json();

        return view('administration.systemstatus', [
            'system_status' => json_decode($system_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ]);
    }

    /**
     * Display PHP information.
     */
    public function php_info(): View
    {
        return view('administration.phpinfo');
    }
}
