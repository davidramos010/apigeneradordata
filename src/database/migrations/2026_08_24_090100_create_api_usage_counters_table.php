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
        Schema::create('api_usage_counters', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type', 10);
            $table->string('subject_id', 64);
            $table->date('usage_date');
            $table->unsignedInteger('request_count')->default(0);
            $table->timestamps();

            $table->unique(['subject_type', 'subject_id', 'usage_date'], 'api_usage_counters_subject_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usage_counters');
    }
};
