<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spk', function (Blueprint $table) {
            $table->string('kode_produksi', 20)->primary();
            $table->string('katalog_produk', 100);
            $table->string('material', 100);
            $table->string('warna', 50);
            $table->integer('jumlah');
            $table->date('tanggal_masuk');
            $table->date('deadline');
            $table->integer('ukuran_s')->default(0);
            $table->integer('ukuran_m')->default(0);
            $table->integer('ukuran_l')->default(0);
            $table->integer('ukuran_xl')->default(0);
            $table->integer('ukuran_xxl')->default(0);
            $table->text('detail')->nullable();
            $table->string('desain', 255)->nullable();
            $table->string('id_pengguna', 20);
            $table->string('id_vendor', 20);
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spk', function (Blueprint $table) {
            $table->dropForeign(['id_pengguna']);
        });

        Schema::dropIfExists('spk');
    }
}
