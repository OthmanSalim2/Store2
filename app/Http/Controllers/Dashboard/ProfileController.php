<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Languages;

class ProfileController extends Controller
{
    // here not important pass id of user to edit function because already logged in website and the information him known.
    public function edit()
    {
        $user = Auth::user();

        return view('dashboard.profile.edit', [
            'user' => $user,
            'countries' => Countries::getNames(),
            'locales' => Languages::getNames(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        // OR they're the same result
        // $user = $request->user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'gender' => ['in:male,female'],
            'country' => ['required', 'string', 'size:2'],
        ]);

        // fill() method it use for two actions on model update or insert
        $user->profile->fill($request->all())->save();

        // another way to update or insert for profile of user.
        // $profile = $user->profile;
        # the the checking process be by any field except the user_id because laravel will pass value to this field if this user found or no.
        // if ($profile->first_name) {
        //     $profile->update($request->all());
        // } else {
        //     // here the user_id already passed to relation profile().
        //     $user->profile()->create($request->all());
        //     // other way
        //     // $request->merge([
        //     //     'user_id' => $user->id,
        //     // ]);
        //     // Profile::create($request->all());
        // }


        return redirect()->route('dashboard.profile.edit')
            ->with('success', 'The Profile updated successfully');
    }
}
