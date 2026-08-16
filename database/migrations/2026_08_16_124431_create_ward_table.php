<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('ward', function (Blueprint $table) {
        $table->increments('uniqueid');
        $table->integer('ward_id');
        $table->string('ward_name', 50);
        $table->integer('lga_id');
        $table->text('ward_description')->nullable();
        $table->string('entered_by_user', 50)->nullable();
        $table->dateTime('date_entered')->nullable();
        $table->string('user_ip_address', 50)->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward');
    }
};
