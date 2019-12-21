<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Poker\User;

class FriendsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('friends')->truncate();
        $data = Config::get('defaultDBData.friends');

        foreach ($data as $relation) {

            $user    = User::where('login', relation[0])->first();
            $idUser2 = Role::where('login', $relation[1])->value('id');
            $user->friends()->attach($id);
        }
    }
}
