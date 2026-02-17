<?php

declare(strict_types=1);

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('title');
            $table->string('owner')->nullable();
            $table->date('due_date')->nullable();
            $table->string('priority')->default(TaskPriority::Medium->value);
            $table->string('status')->default(TaskStatus::Open->value);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'status', 'priority']);
            $table->index('owner');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_tasks');
    }
};
