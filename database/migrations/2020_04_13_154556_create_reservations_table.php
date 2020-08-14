<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('last_name', 100)->comment('Фамилия');
            $table->string('first_name', 100)->comment('Имя');
            $table->string('middle_name', 100)->nullable()->comment('Отчество');
            $table->string('doctor', 255)->nullable();
            $table->string('card', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100);
            $table->date('birth_date')->nullable();
            $table->tinyInteger('sex')->unsigned()->comment('0 - мужской, 1 - женский');
            $table->integer('center_original_id')->unsigned();
            $table->string('test_code', 100);
            $table->dateTime('appointment_time', 0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
