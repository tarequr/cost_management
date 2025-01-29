<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('backend.profile.index');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('backend.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        try {
            $user = User::findOrFail(Auth::id());

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                @unlink(public_path('upload/user_images/' . $user->avatar));
                $filename = 'IMG_' . date('YmdHi') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/user_images'), $filename);
                $user->update([
                    "avatar" => $filename,
                ]);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone'  => $request->phone,
                'gender'  => $request->gender,
                'address'  => $request->address
            ]);

            notify()->success('User Updated Successfully', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('User Update Failed', 'error');
            return back();
        }
    }

    public function changePassword()
    {
        return view('backend.profile.change_password');
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password'         => 'required|confirmed|min:6',
        ]);

        $user = User::findOrFail(Auth::id());
        $hassedPassword = $user->password;

        if (Hash::check($request->current_password, $hassedPassword)) {
            if (!Hash::check($request->password, $hassedPassword)) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                Auth::logout();
                return redirect()->route('login');
            } else {
                notify()->warning('New password can not be as old password!', 'Warning');
            }
        } else {
            notify()->error('Current password not match!', 'Error');
        }
        return back();
    }
}
