<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where("status", 1)
            ->get();

        return view('user.index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return view('user.create', [
            'roles' => Role::query()
                ->where('role_name', '!=', 'Admin')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'employee_name' => 'required|unique:users',
            'email' => 'required|unique:users',
            'mobile' => "required|unique:users",
            'role_id' => 'required',
            'password' => 'required|confirmed'
        ]);

        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->employee_name = $request->employee_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->user_type = 'staff';
            $user->save();

            $userPermission = new UserPermission();
            $userPermission->role_id = $request->role_id;
            $userPermission->user_id = $user->id;
            $userPermission->save();

            DB::commit();
            cacheUserPermission($user->id, true);
            getCachedAgents(true);
            return back()->with('message', "User Created Successfully");
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function edit($id)
    {
        return view('user.edit', [
            'user' => User::findOrFail($id),
            'roles' => Role::query()
                ->where('role_name', '!=', 'Admin')
                ->get(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users,name,' . $request->id,
            'employee_name' => 'required|unique:users,employee_name,' . $request->id,
            'email' => 'required|unique:users,email,' . $request->id,
            'mobile' => "required|unique:users,mobile," . $request->id,
            'password' => 'nullable|confirmed'
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->employee_name = $request->employee_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        if ($request->has("password")) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $userPermission = UserPermission::where('user_id',$request->id)->first();
        $userPermission->role_id = $request->role_id;
        $userPermission->update();
            
        cacheUserPermission($user->id, true);
        getCachedAgents(true);
        return back()->with('message', "User Update Successfully");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->status = 0;
        if ($user->save()) {
            cache()->forget('authBusinessIds-' . auth()->id());
            cache()->forget('agentList');
            return back()->with('message', "User Soft Deleted Successfully");
        }
    }

    public function businessWiseUser()
    {
        return User::query()
            ->get();
    }
}
