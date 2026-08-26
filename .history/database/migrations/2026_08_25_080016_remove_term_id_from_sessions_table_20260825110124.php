<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Drop the foreign key constraint first (name usually follows pattern)
            $table->dropForeign(['term_id']); 
            // Drop the column
            $table->dropColumn('term_id');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('term_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
