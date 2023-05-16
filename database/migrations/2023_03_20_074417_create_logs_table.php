<?php

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string("uuid")->unique()->nullable();
            $table->text('title');
            $table->text('log');
            $table->tinyInteger("status")->default(0); // 0=menunggu approval head departemen,1=menunggu approval head departemen tujuan,2=ditolak,3=dalam pengerjaan departemen tujuan,4=hasil dalam review head Departemen,5=selesai
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Division::class);
            $table->integer("next_approver")->nullable();
            $table->date("due_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
