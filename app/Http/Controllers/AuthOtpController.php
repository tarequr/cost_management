<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SendMail;
use App\Models\UserOtp;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;

class AuthOtpController extends Controller
{
    public function generateOtp(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Store user data in session
            $userData = $request->only(['name', 'email', 'password']);
            Session::put('user_data', $userData);

            // Generate OTP and expiry time
            $otp = rand(100000, 999999);
            $expiredTime = now()->addMinutes(3);

            // Update or create OTP entry
            $userOtp = UserOTP::updateOrCreate(
                ['email' => $request->email],
                ['otp' => $otp, 'expire_at' => $expiredTime]
            );

            Session::put('user_OTP_id', $userOtp->id);

            // Send OTP via email
            Mail::raw("Your OTP is: $otp. It is valid for 3 minutes.", function ($message) use ($userData) {
                $message->to($userData['email'])
                        ->subject('Your OTP Code');
            });

            // Redirect with query parameters
            return redirect()->route('otp.index', [
                'auth' => $userData['name'],
                'returnUrl' => Route::getFacadeRoot()->current()->uri()
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Something Went', 'Error');
            return back();
        }
    }

    public function index()
    {
        $otp_id = Session::get('user_OTP_id');
        $userOtp = UserOtp::findOrFail($otp_id);

        $expirationTime = $userOtp->expire_at;
        $message = "Verification code sent to your mail address " . $userOtp->email;

        return view('auth.register_otp', compact('message', 'userOtp', 'expirationTime'));
    }

    public function verifyOtp()
    {
        return view('auth.register_otp');
    }

    public function checkVarification(Request $request)
    {
        $user = Session::get('user_data');
        $password = $user['password'];
        $userOtp = UserOtp::findOrFail($request->id);


        if ($request->otp_digit == $userOtp->otp) {
            $user = User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($password)
            ]);

            $userOtp->update(['varified' => 1]);

            // $testMailData = [
            //     'title' => 'Registration Verification',
            //     'body' => 'This is Registration Verification',
            //     'name' => $user['name'],
            //     'email' => $user['email'],
            //     'password' => $password,
            //     // 'reset_link' => $resetPasswordLink
            // ];
            // Mail::to($testMailData['email'])->send(new SendMail($testMailData));


            // Send Registration Verification Email
            // Mail::raw("Registration Verification\n\nThis is your registration verification.\n\nName: {$user['name']}\nEmail: {$user['email']}\nPassword: {$password}", function ($message) use ($user) {
            //     $message->to($user['email'])
            //             ->subject('Registration Verification');
            // });


            Session::put('email', $user['email']);
            Session::forget(['user_data', 'user_OTP_id']);

            return response()->json(['message' => 'success'], 200);
        }

        return response()->json(['message' => 'OTP not matched'], 404);
    }

    public function resend(Request $request)
    {
        try {
            $otp = rand(100000, 999999);
            $userOtp = UserOtp::findOrFail($request->id);

            $expiredTime = Carbon::now()->addSeconds(180);
            $userOtp->update([
                'otp' => $otp,
                'expire_at' => $expiredTime,
            ]);

            // Send OTP via email
            Mail::raw("Your OTP is: $otp. It is valid for 3 minutes.", function ($message) use ($userOtp) {
                $message->to($userOtp->email)
                        ->subject('Your OTP Code');
            });

            return redirect()->route('otp.index', [
                'auth' => session()->get('user_data.name'),
                'returnUrl' => Route::getFacadeRoot()->current()->uri()
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Something Went', 'Error');
            return back();
        }
    }

    public function verified()
    {
        return view('auth.email_verified');
    }
}
