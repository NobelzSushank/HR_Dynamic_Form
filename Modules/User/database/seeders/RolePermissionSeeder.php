<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage users',
            'manage roles',
            'manage forms',
            'publish forms',
            'view submissions',
            'export submissions',
            'submit forms',
            'view own submissions',
        ]; 
        
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }
            
        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $hr = Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'api']);
        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'api']);
        
        // Assign permissions
        $admin->givePermissionTo(Permission::all());
        $hr->givePermissionTo(['manage forms','publish forms','view submissions','export submissions']);
        $employee->givePermissionTo(['submit forms','view own submissions']);
    }
}
