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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');        // relasi ke users
            $table->unsignedBigInteger('barang_id');      // relasi ke barangs
            $table->string('nama_peminjam');              // dari form user
            $table->text('alasan_meminjam');
            $table->integer('jumlah');                    // jumlah barang dipinjam
            $table->date('tanggal_pinjam');      
            $table->date('tanggal_kembali');         // tanggal mulai pinjam
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');
            $table->timestamps();
        
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
