<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->nullable()->default(NULL);
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language', 10);
            $table->string('key', 100);
            $table->text('value')->nullable()->default(NULL);
            $table->integer('parent')->unsigned()->nullable()->default(NULL);
            $table->integer('translation_group')->unsigned();
            $table->boolean('flagged')->nullable()->default(NULL);
            $table->timestamps();

            $table->unique(['language', 'key', 'translation_group', 'parent']);

            $table->foreign('parent')->references('id')->on('translations')->onDelete('SET NULL');
            $table->foreign('translation_group')->references('id')->on('translation_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('translations', function(Blueprint $table){
            $table->dropForeign('translations_translation_group_foreign');
            $table->dropForeign('translations_parent_foreign');
        });
        Schema::dropIfExists('translations');
        Schema::dropIfExists('translation_groups');
    }
}
