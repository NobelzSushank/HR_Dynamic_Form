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
        Schema::create('form_submission_answers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid('submission_id')->constrained('form_submissions')->cascadeOnDelete();
            $table->foreignUuid('form_field_id')->constrained('form_fields')->cascadeOnDelete();
            $table->text('value_text')->nullable();
            $table->decimal('value_number', 15, 4)->nullable();
            $table->date('value_date')->nullable();
            $table->json('value_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_answers');
    }
};
