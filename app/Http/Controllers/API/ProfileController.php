<?php

namespace App\Http\Controllers\API;

use App\Repository\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;


class ProfileController extends Controller
{
    public function info()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], ApiHelper::SUCCESS_STATUS);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $user = Auth::user();
        $user->update($request->all());

        return response()->json(['success' => $user], ApiHelper::SUCCESS_STATUS);
    }

    public function password_update(Request $request) {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|confirmed||different:old_password',
            'old_password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $user = Auth::user();
        // https://laracasts.com/discuss/channels/eloquent/old-password-validation
        if (!Hash::check($request->get('old_password'), $user->password)) {
            return response()->json(['error'=> ['old_password' => 'Old password not valid']], 401);
        }
        $user->update([
            'password' => bcrypt($request->get('new_password'))
        ]);

        return response()->json(['success' => 'Ok'], ApiHelper::SUCCESS_STATUS);
    }

}
