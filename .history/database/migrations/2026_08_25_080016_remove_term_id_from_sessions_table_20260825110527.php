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
        // Get the actual constraint name from SQL Server
        $tableName = 'sessions';
        $foreignKey = 'term_id';
        
        // Find the actual constraint name
        $constraintName = DB::select("
            SELECT fk.name 
            FROM sys.foreign_keys AS fk
            INNER JOIN sys.tables AS t ON fk.parent_object_id = t.object_id
            INNER JOIN sys.columns AS c ON fk.parent_object_id = c.object_id AND fk.parent_column_id = c.column_id
            WHERE t.name = '$tableName' AND c.name = '$foreignKey'
        ");

        if (!empty($constraintName)) {
            Schema::table('sessions', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName[0]->name);
            });
        }

        // Now drop the column
        Schema::table('sessions', function (Blueprint $table) {
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
