<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $data = Config::get('defaultDBData.default_users');

        foreach ($data as $user) {

            $newUser = [
                'name'     => $user['name'],
                'login'    => $user['login'],
                'password' => bcrypt($user['password']),
                'victory'  => $user['victory'],
                'gameover' => $user['gameover'],
                'balance'  => $user['balance'],

            ];

            $newUser = new User($newUser);
            $newUser->save();
        }
    }
}
