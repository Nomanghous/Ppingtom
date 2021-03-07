<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\RegisterUserApiRequest;
use App\Http\Requests\LoginUserApiRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles'])->get());
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    

    public function getById($id)
    {

        if (User::where('id', $id)->exists()) {
            $user = User::where('id', $id)->get();
            
            return response()->json(
                [
                    'status_code' => 200,
                    'message' => 'success',
                    'data' => [
                        'user' => $user,
                    ]
                ]
            );
          } else {
            return response()->json([
              "message" => "user not found"
            ], 404);
          }
        
    }
    
    public function register(RegisterUserApiRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', [2]));
        $token =$user->createToken("remember_token")->plainTextToken;
        $user->save();
        // return $user;
        return response()->json(
            [
                'status_code' => 200,
                'message' => 'success',
                'data' => [
                    'user' => $user,
                    'access_token' => $token
                ]
            ]
        );
    }
    
    public function login(LoginUserApiRequest $request)
    {
    
        //Store Email field Value
        $loginValue = $request->input('email');

        //Get Login Type
        $login_type = 'email';

        //Change request type based on user input
        $request->merge([
            $login_type => $loginValue
        ]);

        //Check Credentials and redirect
        $auth = Auth::attempt($request->only($login_type, 'password'));
        if (!$auth) {
            return response()->json(
                [
                    'status_code' => 401,
                'message' => 'Email or Password Incorrect.'
                ]
            );
        }else{
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken("remember_token")->plainTextToken;
            $user->remember_token = $token;
            return response()->json(
                [
                    'status_code' => 200,
                    'message' => 'success',
                    'data' => [
                        'user' => $user,
                        'access_token' => $token
                    ]
                ]
            );
        }
        
    }

}
