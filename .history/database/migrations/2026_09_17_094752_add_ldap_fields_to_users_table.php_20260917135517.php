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
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure ldap_id exists and is nullable
            if (!Schema::hasColumn('users', 'ldap_id')) {
                $table->string('ldap_id')->nullable()->after('username');
            }

            // Make email nullable (not all users have emails)
            $table->string('email')->nullable()->change();

            // Add guid for LDAP synchronization if needed
            if (!Schema::hasColumn('users', 'guid')) {
                $table->string('guid')->nullable()->after('ldap_id');
            }

            // Add domain for LDAP users
            if (!Schema::hasColumn('users', 'domain')) {
                $table->string('domain')->nullable()->after('guid');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ldap_id', 'guid', 'domain']);
        });
    }
};
