<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable(false);
            $table->boolean('reminder_email_sent')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('todos');
    }
};
