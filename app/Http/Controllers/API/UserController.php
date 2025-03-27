<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends BaseController
{
    public function getUser()
    {
        $authUser = Auth::user();
        $user = User::findOrFail($authUser->id);
        $user->avatar = $this->getS3Url($user->avatar);
        return $this->sendResponse($user, 'User');
    }

    public function uploadAvatar(Request $request){
       
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($request->hasFile('image')) {
            $authUser = Auth::user();
            $user = User::findOrFail($authUser->id);
            $extension  = request()->file('image')->getClientOriginalExtension(); //This is to get the extension of the image file just uploaded
            $image_name = time() .'_' . $authUser->id . '.' . $extension;
            $path = $request->file('image')->storeAs(
                'images',
                $image_name,
                's3'
            );
            Storage::disk('s3')->setVisibility($path, "public");
            if(!$path) {
                return $this->sendError($path, 'User profile avatar failed to upload!');
            }
            
            $user->avatar = $path;
            $user->save();
            $success['avatar'] = null;
            if(isset($user->avatar)){
                $success['avatar'] = $this->getS3Url($path);
            }
            return $this->sendResponse($success, 'User profile avatar uploaded successfully!');
        }
       
    }

    public function removeAvatar(){
        $authUser = Auth::user();
        $user = User::findOrFail($authUser->id);
        Storage::disk('s3')->delete($user->avatar);
        $user->avatar = null;
        $user->save();
        $success['avatar'] = null;
        return $this->sendResponse($success, 'User profile avatar removed successfully!');
    }
}