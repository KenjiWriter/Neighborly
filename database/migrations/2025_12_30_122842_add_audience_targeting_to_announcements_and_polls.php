<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('audience_type')->default('community_all')->after('community_id');
            $table->index('audience_type');
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->string('audience_type')->default('community_all')->after('community_id');
            $table->index('audience_type');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex(['audience_type']);
            $table->dropColumn('audience_type');
        });

        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex(['audience_type']);
            $table->dropColumn('audience_type');
        });
    }
};
