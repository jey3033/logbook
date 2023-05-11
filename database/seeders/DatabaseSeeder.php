<?php

namespace Database\Seeders;

use App\Models\Division;
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

        $division = new Division();
        $division->name = "Fromis_9";
        $division->active = 1;
        $division->uuid = md5($division->id . $division->name);
        $division->supervisor = $user->id;
        $division->save();
        

        $user = new User();
        $user->name = "Song Hayoung";
        $user->email = "shy29@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->division = $division->id;
        $user->save();

        $user = new User();
        $user->name = "Park Jiwon";
        $user->email = "pjwtdy@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->division = $division->id;
        $user->save();

        $user = new User();
        $user->name = "Baek Jiheon";
        $user->email = "100jiheon@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->division = $division->id;
        $user->save();

        $division = new Division();
        $division->name = "Le Sserafim";
        $division->active = 1;
        $division->uuid = md5($division->id . $division->name);
        $division->supervisor = User::where('name','Jeremy Jayanto')->first()->id;
        $division->save();

        $user = new User();
        $user->name = "Kim Chaewon";
        $user->email = "chaechae1__@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->division = $division->id;
        $user->save();

        $user = new User();
        $user->name = "Heo Yunjin";
        $user->email = "jennaisante@yopmail.com";
        $user->password = bcrypt("123456789");
        $user->uuid = md5($user->email);
        $user->activated = 1;
        $user->division = $division->id;
        $user->save();
    }
}
