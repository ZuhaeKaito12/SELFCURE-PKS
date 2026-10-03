<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_domain_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2); // percentage 0-100
            $table->string('level'); // maturity level label
            $table->timestamps();

            $table->unique(['assessment_id', 'domain_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_domain_scores');
    }
};
