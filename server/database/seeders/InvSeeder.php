<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('inventory')->insert(['name' => 'Rule1','type' =>'rule','size'=>12323,'checksum'=>'asdasdasda1asd','count'=>123]);
		DB::table('inventory')->insert(['name' => 'Wordlist1','type' =>'wordlist','size'=>123223,'checksum'=>'asdasdasda1asd','count'=>123]);

    }
}
