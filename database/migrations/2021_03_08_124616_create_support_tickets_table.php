<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('assignee')->unsigned()->nullable();
            $table->uuid('ticket_id');
            $table->string('name');
            $table->string('email');
            $table->string('department');
            $table->string('related_service');
            $table->enum('priority', ['low','medium','high'])->default('low');
            $table->enum('status', ['open','replied','close'])->default('open');
            $table->string('subject');
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('assignee')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tickets');
    }
}
