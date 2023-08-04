<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::orderBy('id', 'DESC')->paginate(5);
        return view('users.index', compact('data'));
    }

    //crerate role
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    //store user & role data
    public function store(Request $request)
    {

        //validation
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users, email',
            'password' => 'required|same:confirm_password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User created successfully !');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    public function update(Request $request, $id)
    {
        //validation
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users, email,'.$id,
            'password' => 'required|same:confirm_password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password']))
        {
            $input['password'] = Hash::make($input['password']);
        }
        else
        {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));    
        return redirect()->route('users.index')->with('success','User updated successfully');
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }
}
