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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            // Link the feedback to the authenticated user
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Subject of the feedback (e.g., 'discussion', 'center', 'other')
            $table->string('subject'); 
            
            // Rating from 1 to 5
            $table->unsignedTinyInteger('rating')->default(0); 

            // The main feedback message
            $table->text('message'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};