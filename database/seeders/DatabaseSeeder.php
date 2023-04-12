<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Jeremy Jayanto";
        $user->email = "web.developer@planetgadget.store";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;

        $user->save();

        $user = new User();
        $user->name = "Song Hayoung";
        $user->email = "shy29@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->supervisor = 1;

        $user->save();

        $user = new User();
        $user->name = "Park Jiwon";
        $user->email = "pjwtdy@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->supervisor = 1;

        $user->save();

        $user = new User();
        $user->name = "Baek Jiheon";
        $user->email = "100jiheon@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->supervisor = 1;

        $user->save();
    }
}
