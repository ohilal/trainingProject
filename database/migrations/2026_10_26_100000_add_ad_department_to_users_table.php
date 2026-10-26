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
            if (!Schema::hasColumn('users', 'ad_department')) {
                // Department name parsed from the Active Directory OU
                // (distinguishedName / department attribute) at login.
                $table->string('ad_department')->nullable()->index();
            }

            if (!Schema::hasColumn('users', 'ad_dn')) {
                // Full LDAP distinguished name of the user entry.
                $table->text('ad_dn')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['ad_department', 'ad_dn'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
