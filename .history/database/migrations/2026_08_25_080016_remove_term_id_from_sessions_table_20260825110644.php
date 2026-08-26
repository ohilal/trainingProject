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
    if (Schema::hasColumn('sessions', 'term_id')) {
        try {
            // Try to drop any existing foreign key first
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['term_id']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue anyway
        }
        
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('term_id');
        });
    }
}

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('term_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
