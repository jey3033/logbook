<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;

class PGSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Head Office
        $user = new User();
        $user->name = "Aguswari Ziliwu";
        $user->email = "aguswari.ziliwu@planetgadget.store";
        $user->password = bcrypt('PGGM2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Head Office";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Aguswari Ziliwu")->first()->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Office Cabang Jakarta
        $division = new Division();
        $division->name = "Office Cabang Jakarta";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Aguswari Ziliwu")->first()->id;
        $division->save();
        // Operational
        $user = new User();
        $user->name = "Novan Tri Wandana";
        $user->email = "retail.mgr@planetgadget.store";
        $user->password = bcrypt('PGRetail2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Operational";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Novan Tri Wandana")->first()->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Pusat (Accounting & Tax)
        $division = new Division();
        $division->name = "Pusat (Accounting & Tax)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Aguswari Ziliwu")->first()->id;
        $division->save();
        // Pusat (Finance)
        $user = new User();
        $user->name = "Sherlie Agustina";
        $user->email = "fa.finmgr@planetgadget.store";
        $user->password = bcrypt('PGFinance2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Pusat (Finance)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Sherlie Agustina")->first()->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Pusat (HRD)
        $user = new User();
        $user->name = "Dian Pramudiani";
        $user->email = "hr.admin@planetgadget.store";
        $user->password = bcrypt('GMRetail2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Pusat (HRD)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Pusat (Internal Audit)
        $division = new Division();
        $division->name = "Pusat (Internal Audit)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        // Pusat (IT)
        $division = new Division();
        $division->name = "Pusat (IT)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        // Pusat (Legal)
        $user = new User();
        $user->name = "Lorando";
        $user->email = "legal@planetgadget.store";
        $user->password = bcrypt('PGLegal2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Pusat (Legal)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = User::where('name', "Lorando")->first()->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Pusat (Marketing)
        $division = new Division();
        $division->name = "Pusat (Marketing)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        // Pusat (Product Management)
        $division = new Division();
        $division->name = "Pusat (Product Management)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        // Pusat (Project)
        $division = new Division();
        $division->name = "Pusat (Project)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        // Pusat (Talent Management)
        $user = new User();
        $user->name = "Kadek Juni Sarinadi";
        $user->email = "mkt.creative2@planetgadget.store";
        $user->password = bcrypt('PGTalent2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Pusat (Talent Management)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();

        $user->division = $division->id;
        $user->save();
        // Pusat (Warehouse)
        $user = new User();
        $user->name = "Antiani";
        $user->email = "wh.spv@planetgadget.store";
        $user->password = bcrypt('PGWarehouse2023!@');
        $user->uuid = md5($user->name.$user->email);
        $user->activated = 1;
        $user->save();

        $division = new Division();
        $division->name = "Pusat (Warehouse)";
        $division->active = 1;
        $division->uuid = md5(rand() . $division->name . rand());
        $division->supervisor = $user->id;
        $division->save();
        
        $user->division = $division->id;
        $user->save();
    }
}
