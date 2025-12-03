<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) { 
            $table->id(); 

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('photo')->nullable();
            $table->string('nama_lengkap', 100)->nullable();  
            $table->string('email', 100)->nullable(); 
            $table->string('nomor_telepon', 15)->nullable(); 
            $table->date('tanggal_lahir')->nullable();  
            $table->text('alamat')->nullable(); 
            $table->date('tanggal_masuk')->nullable(); 

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');  

            $table->timestamps(); 

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
