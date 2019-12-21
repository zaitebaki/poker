<?php

use Illuminate\Database\Seeder;
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

            dump($relation[0]);
            dump($relation[1]);

            $user    = User::where('login', $relation[0])->first();
            $idUser2 = User::where('login', $relation[1])->value('id');
            $user->friends()->attach($idUser2);
        }
    }
}
