<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class UserController extends Controller
{
    /**
     * creating login token
     */
    public function authenticate(Request $request){
        /**
         * user login credentials(login)
         * @var $credentials
         */
        $credentials=$request->only('email','password');

        try{
            if(!$token=JWTAuth::attempt($credentials)){
                return response()->json(['Error'=>'Invalid credentials'],400);
            }
        }catch(JWTException $e){
            return response()->json(['Error'=>'could not create token'],500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request){
        $validator=Validator::make($request->all(),[
            'name'      =>'required|string|max:255',
            'email'     =>'required|string|max:255|unique:users',
            'password'  =>'required|string|min:8|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        /**
         * add user to db
         * @var $user
         */
        $user=User::create([
            'name'      =>$request->get('name'),
            'email'     =>$request->get('email'),
            'password'  =>Hash::make($request->get('password')),
        ]);
        /**
         * create token from user registration details
         * @var $token
         * return response
         */
        $token=JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
                
    }

    public function getAuthenticatedUser(){
        try{
            if(!$user=JWTAuth::parseToken()->authenticate()){
                return response()->json(['user not found'],404);
            }
        }catch(Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            return response()->json(['token expired'],$e->getStatusCode());
        }catch(Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return response()->json(['invalid token'],$e->getStatusCode());
        }catch(Tymon\JWTAuth\Exceptions\JWTException $e){
            return response()->json(['token absent'],$e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
}
