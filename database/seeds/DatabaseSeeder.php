<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TagSeeder::class);
        $this->call(AdjectiveSeeder::class);
        $this->call(NounSeeder::class);
    }
}
