<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('todo_id');
            $table->foreign('todo_id')->references('id')->on('todos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('subject')->nullable(false);
            $table->string('recipient')->nullable(false);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('email_logs');
    }
};
