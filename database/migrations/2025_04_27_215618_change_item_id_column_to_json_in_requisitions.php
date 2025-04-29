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
        // Change the 'item_id' column type to JSON
        $table->json('item_id')->change();
    });
}

public function down(): void
{
    Schema::table('requisitions', function (Blueprint $table) {
        // Revert 'item_id' column type back to LONGTEXT if rolling back
        $table->longText('item_id')->nullable()->change();
    });
}
};
