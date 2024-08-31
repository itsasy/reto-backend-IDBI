<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('serie')->nullable()->after('id');
            $table->string('number')->nullable()->after('serie');
            $table->string('type')->nullable()->after('number');
            $table->string('currency')->nullable()->after('type');
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
