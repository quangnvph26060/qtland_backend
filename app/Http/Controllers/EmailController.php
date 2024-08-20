<?php

namespace App\Http\Controllers;

use App\Mail\SampleMail;
use App\Mail\SendPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',

        ]);

        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'name' => $request->input('name')
        ];

        Mail::to($request->input('email'))->send(new SampleMail($data));

        return response()->json(['message' => 'Email sent successfully!']);
    }

    public function sendEmailPassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',

        ]);

        $user = User::where('email', $request->email)->first();
        if($user){
            $data = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            Mail::to($request->input('email'))->send(new SendPasswordMail($data));

            return response()->json($user, 200);
        }else{
            return response()->json(['error', 'Email này chưa đăng ký'], 404);
        }


    }
}
