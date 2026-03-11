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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string('format');
            $table->string('path');
            $table->unsignedBigInteger('size');
            $table->integer('download_count')->default(0);
            $table->enum("visibility", ['private', 'public'])->default('private');
            $table->timestamps();
            $table->softDeletes();
            // indexes  
            $table->index("visibility");
            $table->index('download_count');
            $table->index('size');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
