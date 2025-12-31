<?php

namespace Modules\DynamicForm\Database\Seeders;

use Illuminate\Database\Seeder;

class DynamicFormDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            FormSeeder::class,
            SubmissionSeeder::class,
        ]);
    }
}
