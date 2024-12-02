<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MyprofileController extends Controller
{
    public function index()
    {
        return view('general.myprofile');
    }

    public function update(Request $request, User $user)
    {
        $validator  = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'email_confirmation' => 'required|email|same:email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('panel', 'basic_info');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'location' => $request->location,
            'phone' => $request->phone,

        ]);
        return redirect()->route('myprofile')->with('success', __('messages.update', ['name' => 'Profile']));
    }

    public function changePassword(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('panel', 'password');
        }

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => __('messages.not_register', ['name' => 'current password'])]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('myprofile')->with('success', __('messages.change_password', ['name' => 'Profile']));
    }
}
