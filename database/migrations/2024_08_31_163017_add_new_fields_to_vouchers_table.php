<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('serie')->nullable();
            $table->string('number')->nullable();
            $table->string('type')->nullable();
            $table->string('currency')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('serie');
            $table->dropColumn('number');
            $table->dropColumn('type');
            $table->dropColumn('currency');
        });
    }
};
