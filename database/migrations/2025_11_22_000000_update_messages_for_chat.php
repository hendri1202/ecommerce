<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('to_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->boolean('is_read')->default(false)->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('to_user_id');
            $table->dropColumn('is_read');
        });
    }
};

