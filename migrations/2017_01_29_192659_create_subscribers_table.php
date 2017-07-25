<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up() {

       Schema::create(Folio::table('subscribers'), function (Blueprint $table) {
               $table->increments('id');
               $table->timestamps();
               $table->softDeletes();
               $table->string('email')->nullable();
               $table->string('name')->nullable();
               $table->string('source')->nullable()->default('web');
               $table->string('campaign')->nullable();
               $table->string('path')->nullable();
               $table->string('ip')->nullable();
       });

       $subscriber = new Subscriber();
       $subscriber->email = "mail@domain.com";
       $subscriber->name = "Nono Martinez Alonso";
       $subscriber->source = "suggestive-drawing";
       $subscriber->campaign = "thesis";
       $subscriber->save();

     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::drop(Folio::table('subscribers'));
     }
 }
