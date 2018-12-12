<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyIsDeliverdToDeliveredAtColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_logs', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('is_delivered');
        });

        DB::table('sms_logs')
            ->where('type', 'send')
            ->where('is_delivered', 1)
            ->update(['delivered_at' => DB::raw('sent_at')]);

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn('is_delivered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->boolean('is_delivered')->nullable()->after('delivered_at');
        });

        DB::table('sms_logs')
            ->where('type', 'send')
            ->whereNotNull('delivered_at')
            ->update(['is_delivered' => 1]);

        Schema::table('sms_logs', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });

    }
}
