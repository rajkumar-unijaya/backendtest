<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\HasApiTokens;
use Validator;
use App\User;
use App\Http\Requests\CreateUserRequest;

class UserController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {

    }

    /**
     * Show the form to show all.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
        $responseInfo =  array();
        $responseInfo['code'] = 200;
        $responseInfo['meta']['pagination']['total'] = $users->count();
        $responseInfo['meta']['pagination']['limit'] = 20;
        $responseInfo['meta']['pagination']['page'] = 1;
        if($users->count()/20 > 1)
        $pageValue =  round($users->count()/20);    
        else
        $pageValue = 1;
        $responseInfo['meta']['pagination']['pages'] = $pageValue;
        $responseInfo['data'] = $users;
        
        return response()->json($responseInfo);
        
        
    }

    /**
     * Store a new request.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        if($valid->fails())
        {
            return response()->json(['error' => $valid->errors()],422);
        }
        $user = User::create([
        'name'=>$request->name,
        'email' => $request->email,
        'password' => $request->password
        ]);
        $accessToken  =  $user->createToken('MyApp')->accessToken;
        $responseInfo =  array();
        $responseInfo['code'] = 200;
        $responseInfo['user'] = $user;
        $responseInfo['accessToken'] = $accessToken;
        $responseInfo['message'] = 'Registration Success.';
        return response()->json($responseInfo);
        
    }
    /**
     * Update the given user.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['error' => 'User not found'],422);
            
        }
        $valid = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users'
        ]);
        if($valid->fails())
        {
            return response()->json(['error' => $valid->errors()],422);
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($user->save()) {
        $responseInfo =  array();
        $responseInfo['code'] = 200;
        $responseInfo['user'] = $user;
        $responseInfo['message'] = 'User Update Success.';
        return response()->json($responseInfo);
        } else {
            return response()->json($responseInfo);
        }
    }
    /**
     * delete the given user.
     *
     * @param  string  $id
     * @return Response
     */
    public function destroy($id)
    { 
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['error' => 'User not found'],422);
        }
        if ($user->delete()) {
            $responseInfo =  array();
            $responseInfo['code'] = 200;
            $responseInfo['user'] = $user;
            $responseInfo['message'] = 'User Deleted Success.';
            return response()->json($responseInfo);
        }
        else {
            return response()->json(['error' => 'User delete failed'],422);
        }
        

    }


    /**
     * upload excel the given user.
     *
     * @param  request
     * @return Response
     */
    public function uploadfile(Request $request)
    {
        
          $valid = Validator::make($request->all(), [
                        'file' => 'required'
                    ]);
                if($valid->fails())
                {
                 return response()->json(['error' => $valid->errors()],422);
                }
                $path = $request->file('file')->getRealPath();
                $data = array_map('str_getcsv', file($path));
                $csv_data = array_slice($data, 1);
                foreach($csv_data as $data)
                {
                    if($data[0] == 'Create')
                    {
                        User::create([
                            'email' => $data[1],
                            'name'=>$data[2],
                            'password' => $data[3]
                            ]);  
                    }
                    else if ($data[0] == 'Update') {
                        $user = User::where('email',$data[1])->first();
                        if($user)
                        {
                        $user->email = $data[1];
                        $user->name = $data[2];
                        $user->password = $data[3];
                    }

                    } 
                    else if ($data[0] == 'Delete') {
                        $user = User::where('email',$data[1])->first();
                        if($user)
                        {
                        $user->delete();
                        }
                    }
                    
                }
            $responseInfo =  array();
            $responseInfo['code'] = 200;
            $responseInfo['message'] = 'csv data uploaded in to db.';
            return response()->json($responseInfo);
    }

}
