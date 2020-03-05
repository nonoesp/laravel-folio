<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsRecipients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up() {

       Schema::create(Folio::table('item_recipients'), function (Blueprint $table) {
               $table->increments('id');
               $table->integer('item_id')->nullable();
               $table->timestamps();
               $table->string('twitter')->nullable();
       });



     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::drop(Folio::table('item_recipients'));
     }
 }
