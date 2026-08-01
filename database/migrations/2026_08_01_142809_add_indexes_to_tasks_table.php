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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('title');
            $table->index(['project_id', 'title']);
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['project_id', 'title']);
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['project_id', 'priority']);
        });
    }
};
