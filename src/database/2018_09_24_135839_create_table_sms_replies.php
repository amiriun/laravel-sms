<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSmsReplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_replies', function(Blueprint $table)
        {
            $table->increments('id');

            $table->string('message_id',30)->nullable();
            $table->text('message');
            $table->string('sender_number',25)->index();
            $table->string('to',25)->index();
            $table->string('connector','30');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sms_replies');
    }
}
