<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->unsignedBigInteger('department_id')->nullable()->after('tanggal_masuk');
            $table->unsignedBigInteger('jabatan_id')->nullable()->after('department_id');

            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->nullOnDelete();

            $table->foreign('jabatan_id')
                ->references('id')
                ->on('positions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['jabatan_id']);
            $table->dropColumn(['department_id', 'jabatan_id']);
        });
    }
};
