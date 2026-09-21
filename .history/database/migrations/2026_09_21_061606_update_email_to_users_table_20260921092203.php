
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
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique index on email
            $table->dropIndex('users_email_unique');

            // Modify email to be nullable without unique constraint
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', 'users', function (Blueprint $table) {
            // Restore unique constraint if needed
            $table->string('email')->nullable()->unique()->change();
        });
    }
};
