<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');

            $table->string('ldap_id')->nullable()->after('username');
        });

        DB::statement('CREATE UNIQUE INDEX users_ldap_id_unique ON users (ldap_id) WHERE ldap_id IS NOT NULL');

        DB::table('users')->update([
            'username' => DB::raw('email'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
            $table->string('email')->nullable()->change();
            $table->dropColumn('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_ldap_id_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'ldap_id']);
            $table->string('email')->unique()->change();
            $table->timestamp('email_verified_at')->nullable();
        });
    }
};