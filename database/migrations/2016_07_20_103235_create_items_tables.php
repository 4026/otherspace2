<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateItemsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'tags',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
            }
        );

        Schema::create(
            'adjectives',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('word');
            }
        );

        Schema::create(
            'adjective_tag',
            function (Blueprint $table) {
                $table->integer('adjective_id')->unsigned();
                $table->integer('tag_id')->unsigned();

                $table->foreign('adjective_id')->references('id')->on('adjectives')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('tag_id')->references('id')->on('tags')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        );

        Schema::create(
            'nouns',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('word');
            }
        );

        Schema::create(
            'noun_tag',
            function (Blueprint $table) {
                $table->integer('noun_id')->unsigned();
                $table->integer('tag_id')->unsigned();

                $table->foreign('noun_id')->references('id')->on('nouns')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('tag_id')->references('id')->on('tags')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        );

        Schema::create(
            'adjective_noun',
            function (Blueprint $table) {
                $table->integer('adjective_id')->unsigned();
                $table->integer('noun_id')->unsigned();

                $table->foreign('adjective_id')->references('id')->on('adjectives')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('noun_id')->references('id')->on('nouns')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        );


        Schema::create(
            'items',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('owner_id')->unsigned();
                $table->integer('adjective_id')->unsigned();
                $table->integer('noun_id')->unsigned();
                $table->timestamps();

                $table->foreign('owner_id')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('adjective_id')->references('id')->on('adjectives')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('noun_id')->references('id')->on('nouns')
                    ->onUpdate('cascade')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('items');

        Schema::drop('noun_tag');
        Schema::drop('adjective_noun');
        Schema::drop('nouns');

        Schema::drop('adjective_tag');
        Schema::drop('adjectives');

        Schema::drop('tags');
    }
}
