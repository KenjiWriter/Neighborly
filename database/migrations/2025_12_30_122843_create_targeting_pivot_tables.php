<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('poll_building_visibility');
        Schema::dropIfExists('poll_unit_visibility');
        Schema::dropIfExists('poll_role_visibility');
        Schema::dropIfExists('announcement_building_visibility');
        Schema::dropIfExists('announcement_unit_visibility');
        Schema::dropIfExists('announcement_role_visibility');

        // Announcement targeting pivot tables
        Schema::create('announcement_role_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->string('role_name');
            $table->unique(['announcement_id', 'role_name'], 'ann_role_uq');
        });

        Schema::create('announcement_unit_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->unique(['announcement_id', 'unit_id'], 'ann_unit_uq');
        });

        Schema::create('announcement_building_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->unique(['announcement_id', 'building_id'], 'ann_bld_uq');
        });

        // Poll targeting pivot tables
        Schema::create('poll_role_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->string('role_name');
            $table->unique(['poll_id', 'role_name'], 'poll_role_uq');
        });

        Schema::create('poll_unit_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->unique(['poll_id', 'unit_id'], 'poll_unit_uq');
        });

        Schema::create('poll_building_visibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->unique(['poll_id', 'building_id'], 'poll_bld_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_building_visibility');
        Schema::dropIfExists('poll_unit_visibility');
        Schema::dropIfExists('poll_role_visibility');
        Schema::dropIfExists('announcement_building_visibility');
        Schema::dropIfExists('announcement_unit_visibility');
        Schema::dropIfExists('announcement_role_visibility');
    }
};
