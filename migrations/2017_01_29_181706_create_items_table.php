<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

      Schema::create(Folio::table('items'), function (Blueprint $table) {
              $table->increments('id');
              $table->integer('user_id')->nullable();
              $table->timestamps();
              $table->softDeletes();
              $table->dateTime('published_at')->nullable();
              $table->string('title')->nullable();
              $table->text('text')->nullable();
              $table->string('image')->nullable();
              $table->string('image_src')->nullable();
              $table->string('video')->nullable();
              $table->string('tags_str')->nullable();
              $table->string('slug')->nullable();
              $table->string('slug_title')->nullable();
              $table->string('link')->nullable();
              $table->string('template')->nullable();
              $table->integer('visits')->nullable();
              $table->string('recipients_str', 512)->nullable();
              $table->integer('rss')->nullable();
      });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Folio::table('items'));
    }
}
