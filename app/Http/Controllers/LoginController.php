<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

use App\User;
use App\Http\Resources\UserResource;

use Validator;

class LoginController extends Controller
{

    /****
     * Description : User login in to the system
     * function : login
     * parameters : request
     * return : usersinfo and accessToken
     * 
     */
    public function login(Request $request) {
    	$validator = Validator::make($request->all(), [
                        'email' => 'required|email',
                        'password' => 'required'
                    ]);
    	$validationFailed = $validator->fails();
        $validationErrors = $validator->errors()->all();

        if ($validationFailed) {
            return response()->json(['error' => $valid->errors()],422);  
        }
        $user = User::where('email',$request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'password is incorrect'],422);   
        } else {
            $token = $user->createToken('accces_token')->accessToken;
            $responseInfo =  array();
            $responseInfo['code'] = 200;
            $responseInfo['data'] = new UserResource($user);
            $responseInfo['message'] = 'Login Success.';
            $responseInfo['authToken'] = $token;
        }
        return response()->json($responseInfo);
    }

}
