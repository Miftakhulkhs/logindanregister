<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenggunaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->string('id_pengguna', 20)->primary();
            $table->string('nama', 50);
            $table->string('username', 15)->unique();
            $table->string('no_hp', 20)->nullable();
            $table->string('password', 255);
            $table->string('level', 50);
            $table->integer('otp')->nullable(); 
            $table->timestamp('otp_created_at')->nullable(); 
            $table->timestamp('otp_verified_at')->nullable();
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
        Schema::dropIfExists('pengguna');
    }
}
