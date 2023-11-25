<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Mail\RegistrationSuccessMail;
use App\Mail\UserReportMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateAccountController extends Controller
{
    public function index()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        // User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // Hash the password
        $passwordHashed = Hash::make($request->input('password'));

        // Insert data into the 'users' table using the DB facade
        DB::table('users')->insert([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $passwordHashed,
            // Add other columns if needed
        ]);

        $details = [
            'name'=> $request->input('name'),
            'email'=> $request->input('email'),
        ];
        
        // Mail::to($request->email)->send(new RegistrationSuccessMail($details)); 
        // Mail::to('admin@gmail.com')->send(new UserReportMail($details)); 
   
        dispatch(new SendMailJob($details));
    
        return redirect()->back()->with('success', 'Registration completed');
    }
}