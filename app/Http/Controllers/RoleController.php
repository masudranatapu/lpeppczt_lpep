<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleHasPermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        return view('role.index', [
            'roles' => Role::query()->get(),
        ]);
    }

    public function create()
    {
        return view('role.create', []);
    }

    public function store(Request $reqeust)
    {
        $permission_list = [];
        foreach ($reqeust->permissions as $permission) {
            if ($permission) {
                array_push($permission_list, $permission);
            }
        }

        $role = new Role();
        $role->role_name = $reqeust->role_name;
        $role->save();

        $permission = new RoleHasPermission();
        $permission->role_id = $role->id;
        $permission->permissions = json_encode($permission_list);
        $permission->save();

        return redirect()->route('role.index')->with('message', "New Role Created Successfully");
    }

    public function edit($id)
    {
        return view('role.edit', [
            'role' => Role::query()->find($id),
            'permission' => RoleHasPermission::query()->where('role_id', $id)->first(),
        ]);
    }

    public function update(Request $reqeust)
    {
        $permission_list = [];
        foreach ($reqeust->permissions as $permission) {
            if ($permission) {
                array_push($permission_list, $permission);
            }
        }

        $role = Role::findOrFail($reqeust->role_id);
        $role->role_name = $reqeust->role_name;
        $role->save();

        $permission = RoleHasPermission::where('role_id', $reqeust->role_id)->first();
        $permission->role_id = $role->id;
        $permission->permissions = json_encode($permission_list);
        $permission->save();

        $userIds = User::query()
            ->whereHas('userPermission', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            })
            ->select('id')
            ->get();

        cache()->forget('user-permission-' . auth()->id());
        foreach ($userIds as $uid) {
            cache()->forget('user-permission-' . $uid);
        }

        return redirect()->route('role.index')->with('message', "Role Updated Successfully");
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $user_permission = UserPermission::where('role_id', $id)->get();
        if ($user_permission->count() > 0) {
            return back()->with([
                'type' => 'error',
                'message' => "Already Assign A User In This Role"
            ]);
        }
        RoleHasPermission::where('role_id', $id)->delete();
        if ($role->delete()) {
            return back()->with('message', "Role Deleted Successfully");
        }
    }
}
