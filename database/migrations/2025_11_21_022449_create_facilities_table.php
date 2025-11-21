<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'Discussion Room A'
            $table->text('description')->nullable();
            $table->string('type'); // e.g., 'room', 'pad', 'equipment'
            // Corresponds to the staff use case 'Update facilities Status'
            $table->string('status')->default('available'); // 'available', 'not available', 'under maintenance' 
            $table->integer('capacity')->default(1); // Max number of people/users
            $table->timestamps();
        });
    }

    // ... down() method
};
