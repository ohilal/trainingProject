<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique()->after('id');
                $table->string('ldap_id')->nullable()->unique()->after('username');
                $table->string('guid')->nullable()->after('ldap_id');
                $table->boolean('is_ldap_user')->default(false)->after('guid');
                
                // Make email nullable as not all users have it
                $table->string('email')->nullable()->change();
                // Password is not used for LDAP users, but kept for potential local admins
                $table->string('password')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'ldap_id', 'guid', 'is_ldap_user']);
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};