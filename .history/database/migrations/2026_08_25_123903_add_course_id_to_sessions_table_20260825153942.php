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
        // Add course_id foreign key
        $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('sessions', function (Blueprint $table) {
        $table->dropForeign(['course_id']);
        $table->dropColumn('course_id');
    });
}
};
