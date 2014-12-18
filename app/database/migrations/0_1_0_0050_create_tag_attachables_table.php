<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Pivot table for polymorphic many to many relation
 */
class CreateTagAttachablesTable extends Migration {

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_attachables', function(Blueprint $table)
        {
            $table->bigIncrements('id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->bigInteger('tag_attachable_id')->unsigned();
            $table->string('tag_attachable_type');
            $table->timestamps();
            $table->foreign('tag_id', 'tag_attachables_tag_id_foreign')->references('id')->on('tags')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_attachables');
    }
}
