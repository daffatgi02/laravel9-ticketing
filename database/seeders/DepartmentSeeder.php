<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Human Capital', 'code' => 'HC'],
            ['name' => 'Information Technology', 'code' => 'IT'],
            ['name' => 'General Affairs', 'code' => 'GA'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Operations', 'code' => 'OPS'],
            ['name' => 'Sales', 'code' => 'SLS'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
