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

            $table->string('username')->nullable();
            $table->string('email')->primary();
            $table->string('password')->nullable();
            $table->string('avatar')->default(env('APP_URL') ."/storage/avatars/default.png");
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
