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
    Schema::create('announced_lga_results', function (Blueprint $table) {
        $table->increments('result_id');
        $table->string('lga_name', 50); // misleading name — actually stores the lga_id as a string
        $table->string('party_abbreviation', 4);
        $table->integer('party_score');
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
        Schema::dropIfExists('announced_lga_results');
    }
};
