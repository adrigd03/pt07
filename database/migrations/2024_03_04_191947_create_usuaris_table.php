<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
     /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuaris', function (Blueprint $table) {

            $table->string('nom') ->nullable(); 
            $table->string('cognoms')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->primary();
            $table->string('password')->nullable();
            $table->boolean('admin')->default(0);
            $table->timestamps();

        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuaris');
    }
};
