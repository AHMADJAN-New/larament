<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_counters', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedInteger('last_meeting_no')->default(0);
        });

        DB::table('meeting_counters')->insert([
            'id' => 1,
            'last_meeting_no' => (int) (DB::table('meetings')->max('meeting_no') ?? 0),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_counters');
    }
};
