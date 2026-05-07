<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->unique();
            $table->timestampsTz();
        });

        Schema::create('key_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('key_id')
                ->constrained('keys')
                ->cascadeOnDelete();
            $table->text('value');
            $table->timestampTz('recorded_at');
            $table->timestampsTz();

            $table->index(['key_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('key_values');
        Schema::dropIfExists('keys');
    }
};