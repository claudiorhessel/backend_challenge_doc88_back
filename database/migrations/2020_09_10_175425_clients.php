<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Clients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('clients', function ( Blueprint $table ) {
            $table->id();

            $table->string('name', 250);
            $table->string('email', 100);
            $table->string('phone', 20);
            $table->date('birth_date');
            $table->string('address', 250);
            $table->string('complement', 250)->nullable();
            $table->string('neighborhood', 250);
            $table->string('cep', 9);

            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('clients');
    }
}
