<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->foreignId('cultural_group_id')
                ->nullable()
                ->after('media_id')
                ->constrained('cultural_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teachings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cultural_group_id');
        });
    }
};
