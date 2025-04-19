<?php

namespace Database\Seeders;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // IT Categories
            [
                'name' => 'Hardware Issue',
                'description' => 'Problems with computers, printers, scanners, etc.',
                'assigned_to' => 'IT'
            ],
            [
                'name' => 'Software Issue',
                'description' => 'Problems with applications, operating systems, etc.',
                'assigned_to' => 'IT'
            ],
            [
                'name' => 'Network Issue',
                'description' => 'Problems with internet connection, internal network, etc.',
                'assigned_to' => 'IT'
            ],
            [
                'name' => 'Account Access',
                'description' => 'Problems accessing accounts, password resets, etc.',
                'assigned_to' => 'IT'
            ],
            [
                'name' => 'Software Request',
                'description' => 'Requests for new software or applications',
                'assigned_to' => 'IT'
            ],

            // GA Categories
            [
                'name' => 'Building Maintenance',
                'description' => 'Issues with building infrastructure, lights, AC, etc.',
                'assigned_to' => 'GA'
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Requests for office supplies',
                'assigned_to' => 'GA'
            ],
            [
                'name' => 'Furniture',
                'description' => 'Issues or requests related to office furniture',
                'assigned_to' => 'GA'
            ],
            [
                'name' => 'Meeting Room',
                'description' => 'Requests or issues related to meeting rooms',
                'assigned_to' => 'GA'
            ],
            [
                'name' => 'Facility Issue',
                'description' => 'Issues with office facilities',
                'assigned_to' => 'GA'
            ],
        ];

        foreach ($categories as $category) {
            TicketCategory::create($category);
        }
    }
}
