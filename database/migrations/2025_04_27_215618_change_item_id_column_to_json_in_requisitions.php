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
        Schema::table('requisitions', function (Blueprint $table) {
            // Change 'item_id' column to longText if JSON is not supported
            $table->longText('item_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('requisitions', function (Blueprint $table) {
            // Revert 'item_id' column to longText
            $table->longText('item_id')->nullable()->change();
        });
    }

};
