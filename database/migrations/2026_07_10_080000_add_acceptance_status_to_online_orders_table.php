<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->enum('rider_acceptance_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('delivery_rider_id');
            $table->timestamp('rider_accepted_at')->nullable()->after('rider_acceptance_status');
        });
    }

    public function down()
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropColumn(['rider_acceptance_status', 'rider_accepted_at']);
        });
    }
};
