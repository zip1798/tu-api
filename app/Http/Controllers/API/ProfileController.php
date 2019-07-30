<?php

namespace App\Http\Controllers\API;

use App\Repository\ApiHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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
            return response()->json(['error'=>$validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $user = Auth::user();
        $user->update($request->all());

        return response()->json(['success' => $user], ApiHelper::SUCCESS_STATUS);
    }

    public function updateAvatar(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'image',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
        }

        $user = Auth::user();
//        $file = $request->file('file')->storePublicly('avatar');
        $resized = Image::make($request->file('file'))->fit(256)->encode('jpg');
        $filename = $this->generateHashName($resized->__toString(), "avatar/ava-{$user->id}-", 'jpg');
        if (Storage::put($filename, $resized->__toString(), 'public')) {
            if ($user->icon) {
                Storage::delete($user->icon);
            }
            $user->update(['icon' => $filename]);
        }

        return response()->json(['success' => $user], ApiHelper::SUCCESS_STATUS);
    }

    private function generateHashName($string, $prefix = '', $ext = 'jpg' ) {
        $now = Carbon::now()->toDateTimeString();
        return $prefix . md5($string.$now) . '.' . $ext;
    }

    public function updatePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|confirmed||different:old_password',
            'old_password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], ApiHelper::ERROR_VALIDATE_STATUS);
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
