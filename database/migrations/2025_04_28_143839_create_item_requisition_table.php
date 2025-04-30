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
        Schema::create('item_requisition', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')
                  ->constrained('items')  // Explicitly define the related table
                  ->onDelete('cascade');
            $table->foreignId('requisition_id')
                  ->constrained('requisitions')  // Explicitly define the related table
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_requisition');
    }
};
