<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id')->nullable()->after('email');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('avatar_url')->nullable()->after('facebook_id');
            $table->index('google_id');
            $table->index('facebook_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['google_id']);
            $table->dropIndex(['facebook_id']);
            $table->dropColumn(['google_id', 'facebook_id', 'avatar_url']);
        });
    }
};
