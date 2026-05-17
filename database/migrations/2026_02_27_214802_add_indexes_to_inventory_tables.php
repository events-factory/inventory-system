<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('group_id');
            $table->index('name');
            $table->index('status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('name');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->index('subcategory_id');
            $table->index('name');
        });

        Schema::table('requisitions', function (Blueprint $table) {
            $table->index('event_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['items_category_id_index']);
            $table->dropIndex(['items_subcategory_id_index']);
            $table->dropIndex(['items_group_id_index']);
            $table->dropIndex(['items_name_index']);
            $table->dropIndex(['items_status_index']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['categories_name_index']);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropIndex(['subcategories_category_id_index']);
            $table->dropIndex(['subcategories_name_index']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex(['groups_subcategory_id_index']);
            $table->dropIndex(['groups_name_index']);
        });

        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropIndex(['requisitions_event_id_index']);
            $table->dropIndex(['requisitions_status_index']);
        });
    }
};
