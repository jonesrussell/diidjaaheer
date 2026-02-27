<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultural_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('cultural_groups')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('depth_type'); // root|family|group|sub_group|community|clan
            $table->text('description')->nullable();
            $table->foreignId('media_id')->nullable()->constrained()->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultural_groups');
    }
};
