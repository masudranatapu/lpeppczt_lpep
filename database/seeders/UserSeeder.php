<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUser('Admin', 'admin@gmail.com', 'admin', '01234567890', 1);
        $this->createUser('Agent', 'agent@gmail.com', 'staff', '15822558820', 1);
        $this->createRoles();
        $this->createUserPermissions(1, ROLE_ADMIN);
        $this->createUserPermissions(2, ROLE_AGENT);
    }

    private function createUser($name, $email, $type, $mobile)
    {
        $user = User::query()->create([
            'name' => $name,
            'mobile' => $mobile,
            'email' => $email,
            'password' => Hash::make('123456'),
            'user_type' => $type,
            'status' => 1,
        ]);

        return $user;
    }

    private function createRoles()
    {
        $role = Role::query()->create([
            'role_name' => 'Admin',
        ]);
        RoleHasPermission::query()->create([
            'role_id' => $role->id,
            'permissions' => '["sup","s1","s2","s3","s4","supplier_pay","c1","c2","c3","c4","customer_pay","cg1","pro","p1","p2","p3","p4","pad1","ps1","cat1","cat2","cat3","cat4","bra1","bra2","bra3","bra4","uni1","uni2","uni3","uni4","pur","pu1","pu2","pu3","pu4","stock","st1","sale","sa1","sa2","sa3","sa4","sr1","filterByUser","quotation","q1","q2","q3","report","re1","asset","as1","as2","expense","ex1","ex2","accounts","ac1","ac2","ac3","accountTypeeAdd","accountTypeList","employee","em1","em2","em3","em4","sal1","sal2","user","u1","u2","u3","u4","ro1","ro2","ro3","setting","st1","st2","st3","admin-deposit.index","app","appreport"]',
        ]);


        $role = Role::query()->create([
            'role_name' => 'Staff',
        ]);
        RoleHasPermission::query()->create([
            'role_id' => $role->id,
            'permissions' => '["sup","s1","s2","s3","s4","supplier_pay","c1","c2","c3","c4","customer_pay","cg1","pro","p1","p2","p3","p4","pad1","ps1","cat1","cat2","cat3","cat4","bra1","bra2","bra3","bra4","uni1","uni2","uni3","uni4","pur","pu1","pu2","pu3","pu4","stock","st1","sale","sa1","sa2","sa3","sa4","sr1","filterByUser","quotation","q1","q2","q3","report","re1","asset","as1","as2","expense","ex1","ex2","accounts","ac1","ac2","ac3","accountTypeeAdd","accountTypeList","employee","em1","em2","em3","em4","sal1","sal2","user","u1","u2","u3","u4","ro1","ro2","ro3","setting","st1","st2","st3","admin-deposit.index"]',
        ]);


        $role = Role::query()->create([
            'role_name' => 'Agent',
        ]);
        RoleHasPermission::query()->create([
            'role_id' => $role->id,
            'permissions' => '["sup","c1","c2","c3","c4","customer_pay","cg1","pro","p1","ps1","cat1","cat2","cat3","cat4","bra1","bra2","bra3","bra4","uni1","uni2","uni3","uni4","pur","pu1","pu2","pu3","pu4","stock","st1","sale","sa1","sa2","sa3","sa4","sr1","quotation","q1","q2","q3","expense","ex1","ex2","sal1","sal2","app","appreport"]',
        ]);
    }

    private function createUserPermissions($userId, $roleId)
    {
        UserPermission::query()->create([
            'role_id' => $roleId,
            'user_id' => $userId
        ]);

        UserPermission::query()->create([
            'role_id' => $roleId,
            'user_id' => $userId
        ]);
    }
}
