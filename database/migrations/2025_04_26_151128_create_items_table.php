<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
                $table->foreignId('group_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('model')->nullable();
                $table->enum('status', ['Available', 'Rented', 'Damaged', 'Lost'])->default('Available');
                $table->string('serial_number')->nullable();
                $table->enum('unit', ['Kg', 'Cartons', 'PC', 'L', 'M', 'Sqm'])->default('PC');
                $table->integer('quantity')->default(0);
                $table->string('flight_case_number')->nullable();
                $table->text('remarks')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
