<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $models = [
            'department', 'course', 'session', 'term', 'file',
            'document', 'quiz', 'question', 'rubric', 'feedback',
            'participant', 'myCourse', 'plan', 'user'
        ];

        foreach ($models as $model) {
            // create permissions
            Permission::firstOrCreate(['name' => $model . '.index']);
            Permission::firstOrCreate(['name' => $model . '.create']);
            Permission::firstOrCreate(['name' => $model . '.edit']);
            Permission::firstOrCreate(['name' => $model . '.delete']);
            Permission::firstOrCreate(['name' => $model . '.show']);
        }

        Permission::firstOrCreate(['name' => 'document.order']);
        Permission::firstOrCreate(['name' => 'menu.education']);
        Permission::firstOrCreate(['name' => 'menu.toolbox']);
        Permission::firstOrCreate(['name' => 'mentor.list']);
        


        $role1 = Role::firstOrCreate(['name' => 'Super-Admin']);

        // create role and assign permission to super visor
        $role2 = Role::firstOrCreate(['name' => 'supervisor']);
        foreach ($models as $model) {
            $role2->givePermissionTo($model . '.index');
            $role2->givePermissionTo($model . '.create');
            $role2->givePermissionTo($model . '.edit');
            $role2->givePermissionTo($model . '.show');
        }
        $role2->givePermissionTo('document.order');
        $role2->givePermissionTo('menu.education');
        $role2->givePermissionTo('menu.toolbox');
        $role2->givePermissionTo('mentor.list');

        // create roles and assign existing permissions to mentos
        $role3 = Role::firstOrCreate(['name' => 'mentor']);
        foreach ($models as $model) {
            $role3->givePermissionTo($model . '.index');
            $role3->givePermissionTo($model . '.show');
        }
        $role3->givePermissionTo('menu.education');
        $role3->givePermissionTo('mentor.list');
        

        // create roles and assign existing permissions
        $role4 = Role::firstOrCreate(['name' => 'student']);
        $role4->givePermissionTo('myCourse.index');

        $createDemoUser = function (string $name, string $email) {
            return \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'username' => $email,
                    'password' => Hash::make('password'),
                ]
            );
        };



        // create users
        // participants
        // create demo users


        // Super Admin
        $admin = $createDemoUser('Arash Dehghani', 'arash.aspx@gmail.com');
        $admin->assignRole($role1);

        /*
        * SuperVisor Users
        */

        // SuperVisors Adults
        $supervisor = $createDemoUser('SuperVisor', 'supervisor@laramint.com');
        $supervisor->assignRole($role2);

        // SuperVisors Kids
        $supervisorKids = $createDemoUser('SuperVisor Kids', 'kids_supervisor@laramint.com');
        $supervisorKids->assignRole($role2);

        // SuperVisors Teenage
        $supervisorTeenage = $createDemoUser('SuperVisor Teenage', 'teenage_supervisor@laramint.com');
        $supervisorTeenage->assignRole($role2);


        /*
        * Mentors Users
        */

        // mentor adults
        $mentor = $createDemoUser('mentor', 'mentor@laramint.com');
        $mentor->assignRole($role3);

        // mentor kids
        $mentorKids = $createDemoUser('mentor kids', 'kids_mentor@laramint.com');
        $mentorKids->assignRole($role3);

        // mentor teenage
        $mentorTeenage = $createDemoUser('mentor teenage', 'teenage_mentor@laramint.com');
        $mentorTeenage->assignRole($role3);




        /*
        * students Users
        */

        // adult student
        $student = $createDemoUser('student', 'student@laramint.com');
        $student->assignRole($role4);


         // teenage student
         $studentTeenage = $createDemoUser('teenage', 'teenage@laramint.com');
        $studentTeenage->assignRole($role4);

        
         // kids student
         $studentKids = $createDemoUser('kids', 'kids@laramint.com');
        $studentKids->assignRole($role4);
    }
}
