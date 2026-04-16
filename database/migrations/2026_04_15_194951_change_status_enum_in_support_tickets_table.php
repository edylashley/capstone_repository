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
        // Altering the ENUM natively is the safest to avoid doctrine/dbal issues
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE support_tickets MODIFY COLUMN status ENUM('pending', 'resolved') DEFAULT 'pending'");
        
        // Also ensure any existing 'open' tickets are flipped to 'pending'
        \Illuminate\Support\Facades\DB::table('support_tickets')->where('status', 'open')->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE support_tickets MODIFY COLUMN status ENUM('open', 'resolved') DEFAULT 'open'");
    }
};
