<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        if (Gate::allows('isAdmin') || Gate::allows('isModer')){
            return User::latest()->paginate(3);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'name' => 'required|string|max:191',
           'email' => 'required|string|email|unique:users',
           'password' => 'required|string|min:6',
            'type' => 'required|min:1'
        ], [
            'required' => 'Поле :attribute обязательно!',
            'min' => 'Пароль не может быть менее :min символов',
            'name' => 'Имя не может быть более 191 символов',
            'email' => 'Почта должна быть валидной!',
            'unique' => 'Почта уже существует!'
        ], [
            'name' => 'имя',
            'email' => 'почта',
            'password' => 'пароль',
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = auth('api')->user();
        if ($user->photo == null){
            $user->photo = 'profile.jpg';
        }
        return $user;
    }

    public function updateProfile(Request $request)
    {
        $user =  auth('api')->user();
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
        ]);
        User::UpdateProfile($request);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6',
            'type' => 'required|min:1'
        ], [
            'required' => 'Поле :attribute обязательно!',
            'min' => 'Пароль не может быть менее :min символов',
            'name' => 'Имя не может быть более 191 символов',
            'email' => 'Почта должна быть валидной!',
            'unique' => 'Почта уже существует!'
        ], [
            'name' => 'имя',
            'email' => 'почта',
            'password' => 'пароль',
        ]);

        $user->update($request->all());

        return ['message' => 'ok'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return ['message' => 'User deleted!'];
    }

    public function search(Request $request){

        if ($search = $request->get('q')) {
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('email','LIKE',"%$search%");
            })->paginate(20);
        }else{
            $users = User::latest()->paginate(3);
        }

        return $users;

    }
}
