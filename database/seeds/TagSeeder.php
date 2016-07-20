<?php

use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->insert([
            ['name' => 'old'],
            ['name' => 'new'],
            ['name' => 'opens'],
            ['name' => 'closes'],
            ['name' => 'draws'],
            ['name' => 'precious'],
            ['name' => 'once-alive'],
        ]);
    }
}
