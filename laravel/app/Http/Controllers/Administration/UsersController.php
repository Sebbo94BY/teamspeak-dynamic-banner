<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddRequest;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    /**
     * Display the users view.
     */
    public function users(): View
    {
        return view('administration.users', [
            'users' => User::all(),
            'roles' => Role::all(),
        ]);
    }

    /**
     * Creates a new user model in the database.
     */
    public function create_user(UserAddRequest $request): RedirectResponse
    {
        $initial_password = Str::random(16);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($initial_password);

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
        $user = User::find($request->user_id);

        $user->name = $request->name;
        $user->email = $request->email;

        if (! $user->save()) {
            return Redirect::route('administration.user.edit', ['user_id' => $request->user_id])
                ->withInput($request->all())
                ->with([
                    'error' => 'user-update-error',
                    'message' => 'Failed to update the user in the database. Please try again.',
                ]);
        }

        $user->syncRoles($request->roles);

        return Redirect::route('administration.users')->with([
            'success' => 'user-update-successful',
            'message' => 'Successfully updated the user.',
        ]);
    }

    /**
     * Deletes a single user.
     */
    public function delete_user(UserDeleteRequest $request): RedirectResponse
    {
        $user = User::find($request->user_id);

        if (! $user->delete()) {
            return Redirect::route('administration.users')->with([
                'error' => 'user-delete-error',
                'message' => 'Failed to delete the user from the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.users')->with([
            'success' => 'user-delete-successful',
            'message' => 'Successfully deleted the user.',
        ]);
    }
}
