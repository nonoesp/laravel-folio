<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up() {

       Schema::create('users', function (Blueprint $table) {
               $table->increments('id');
               $table->integer('is_admin')->nullable()->default(0);
               $table->timestamps();
               $table->softDeletes();
               $table->string('name')->nullable();
               $table->string('email')->nullable();
               $table->string('password')->nullable();
               $table->string('remember_token')->nullable();
               $table->string('image')->nullable();
               //$table->text('biography', '');//->nullable();
               $table->string('twitter')->nullable();
               $table->string('twitter_image')->nullable();
       });

       $user = new User();
       $user->name = "Nono Martinez Alonso";
       $user->is_admin = 1;
       $user->email = "mail@domain.com";
       $user->twitter = "nonoesp";
       $user->save();

     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::drop('users');
     }
 }
