<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateChaosProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('chaos_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->intOrBigIntBasedOnRelated('user_id', Schema::connection(null), 'users.id');
            $table->string('some_field')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('chaos_profiles');
    }
}
