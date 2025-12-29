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
        Schema::table('users', function (Blueprint $table) {
            // Verification fields
            $table->string('verification_status')->default('pending')->after('email'); // pending, approved, rejected
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->timestamp('rejected_at')->nullable()->after('verified_at');
            $table->string('rejection_reason')->nullable()->after('rejected_at');

            // Address fields
            $table->string('address_line1')->nullable()->after('rejection_reason');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('postal_code')->nullable()->after('city');

            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);
            $table->dropColumn([
                'verification_status',
                'verified_at',
                'rejected_at',
                'rejection_reason',
                'address_line1',
                'address_line2',
                'city',
                'postal_code',
            ]);
        });
    }
};
