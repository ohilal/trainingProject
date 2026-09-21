<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_ldap_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_ldap_user')->default(false)->after('guid');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_ldap_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_ldap_user');
            });
        }
    }
};