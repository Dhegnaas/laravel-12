<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('audit_trails')) return '';
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->integer('action_id');
            $table->date('date');
            $table->string('related_to');
            $table->text('comment');
            $table->integer('user');
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trails');
    }
};
