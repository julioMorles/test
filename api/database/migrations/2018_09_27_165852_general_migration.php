<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeneralMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo "Corriendo migracion ".__CLASS__."\n";

        echo "Creando tabla roles ".__LINE__."\n";

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->timestamps();
        });

        echo "Creando relacion tabla Users a roles ".__LINE__."\n";

        Schema::create('user_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->timestamps();
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });
        echo "Creando tabla de paises ".__LINE__."\n";

        Schema::create('paises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->timestamps();
        });

        echo "Creando tabla de departamentos ".__LINE__."\n";

        Schema::create('departamentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->unsignedInteger('paise_id');
            $table->timestamps();
        });

        echo "Creando relacion departamentos a gepaises ".__LINE__."\n";

        Schema::table('departamentos', function (Blueprint $table) {
            $table->foreign('paise_id')->references('id')->on('paises');
        });

        echo "Creando tabla de ciudades ".__LINE__."\n";

        Schema::create('ciudades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->unsignedInteger('departamento_id');
            $table->timestamps();
        });

        echo "Creando relacion geciudades a departamentos ".__LINE__."\n";

        Schema::table('ciudades', function (Blueprint $table) {
            $table->foreign('departamento_id')->references('id')->on('departamentos');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
}
