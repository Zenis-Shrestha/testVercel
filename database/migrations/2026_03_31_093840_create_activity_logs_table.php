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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')
              ->nullable()
              ->constrained()
              ->onDelete('set null');   // keep log even if user deleted
        $table->string('action');       // 'created', 'updated', 'deleted', 'login'
        $table->string('model')->nullable();  // 'User', 'Post' etc
        $table->string('model_id')->nullable(); // which record was affected
        $table->text('description');    // human readable "Admin created user John"
        $table->string('ip_address')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
