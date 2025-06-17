<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });
    }
};
