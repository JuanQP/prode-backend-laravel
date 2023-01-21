<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new UserResource(User::findOrFail($id));
    }

    /**
     * Returns the current user detailed data.
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {
        return new UserDetailResource(auth()->user());
    }

    /**
     * Update the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_me(UserRequest $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->fill($request->validated());
        $user->save();

        return new UserDetailResource($user);
    }

    /**
     * Changes the password of a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change_password(ChangePasswordRequest $request, $id)
    {
        /**
         * @var \App\Models\User
         */
        $currentUser = auth()->user();
        if(!$currentUser->is_staff && $currentUser->id != $id) {
            return response()->json([
                'message' => 'No tenés permisos'
            ], 401);
        }
        // Change password
        $newPassword = $request->safe()['password'];
        $user = User::findOrFail($id);
        $user->password = Hash::make($newPassword);
        $user->save();

        return new UserDetailResource($user);
    }
}
