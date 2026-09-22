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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'end_date')) {
                $table->date('end_date')->nullable();
            }

            if (! Schema::hasColumn('events', 'image')) {
                $table->string('image')->nullable();
            }

            if (! Schema::hasColumn('events', 'image_folder')) {
                $table->string('image_folder')->nullable();
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
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
