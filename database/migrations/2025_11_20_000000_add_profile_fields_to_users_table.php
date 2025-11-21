<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('role');
            $table->string('province')->nullable()->after('phone');
            $table->string('city')->nullable()->after('province');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->text('address')->nullable()->after('postal_code');
            $table->string('avatar')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'province', 'city', 'postal_code', 'address', 'avatar']);
        });
    }
};

