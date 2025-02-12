<?php

use App\Enum\DocumentVisibility;
use App\Enum\Language;
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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('author')->index();
            $table->string('title')->index();
            $table->string('path');
            $table->unsignedMediumInteger('downloads')->default(0);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('pages');
            $table->enum('language', Language::values());
            $table->enum('visibility', DocumentVisibility::values());
            $table->string('token')->nullable();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
