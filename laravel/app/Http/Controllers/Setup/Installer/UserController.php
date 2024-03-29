<?php

namespace App\Http\Controllers\Setup\Installer;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallerAddUserRequest;
use App\Mail\SetupInstallerCompleted;
use App\Models\Localization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the view.
     */
    public function show_view(): RedirectResponse|View
    {
        if (User::all()->count() > 0) {
            return Redirect::route('dashboard');
        }

        return view('setup.installer.user', [
            'localizations' => Localization::all(),
        ]);
    }

    /**
     * Creates the initial user.
     */
    public function create(InstallerAddUserRequest $request): RedirectResponse
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->localization_id = $request->localization_id;

        if (! $user->save()) {
            return Redirect::route('setup.installer.user', ['locale' => $request->route()->parameter('locale')])->withInput($request->all())->with([
                'error' => 'user-add-error',
                'message' => 'Failed to save the new data set into the database. Please try again.',
            ]);
        }

        // Grant the initial user the 'Super Admin' role
        $user->assignRole('Super Admin');

        // Authenticate user immediately to avoid the login mask
        Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $request->session()->regenerate();

        Mail::to($user)->send(new SetupInstallerCompleted($user));

        return Redirect::route('dashboard')->with([
            'success' => 'installer-successful',
            'message' => 'Successfully finished the installation. Enjoy the application!',
        ]);
    }
}
