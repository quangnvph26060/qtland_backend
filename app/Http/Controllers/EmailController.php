<?php

namespace App\Http\Controllers;

use App\Mail\SampleMail;
use Illuminate\Http\Request;
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
}
