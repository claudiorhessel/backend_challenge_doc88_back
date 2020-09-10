<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('clients', function ( Blueprint $table ) {
            $table->id()->autoIncrement();

            $table->string('name', 250);
            $table->string('email', 100);
            $table->integer('phone')->unsigned();
            $table->date('birth_date');
            $table->string('address', 250);
            $table->string('complement', 250)->nullable();
            $table->string('neighborhood', 50);
            $table->integer('cep')->unsigned();

            $table->timestamps();
            $table->softDeletes();
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
