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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title');       // You must write this explicitly
        $table->text('description');   // You must write this explicitly
        $table->foreignId('event_type_id')->constrained(); // Foreign key
        $table->date('start_date');
       $table->date('end_date')->nullable();
       $table->string('image')->nullable();
       $table->string('image_folder')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
