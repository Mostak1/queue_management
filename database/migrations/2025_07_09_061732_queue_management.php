<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Creating booths table
        Schema::create('booths', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Creating customers (queue tickets) table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booth_id')->nullable();
            $table->string('serial_number')->unique();
            $table->enum('status', ['waiting', 'called', 'served', 'skipped'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->foreign('booth_id')->references('id')->on('booths')->onDelete('set null');
            $table->timestamps();
        });

        // Creating skipped customers table
        Schema::create('skipped_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('booth_id');
            $table->timestamp('skipped_at');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('booth_id')->references('id')->on('booths')->onDelete('cascade');
            $table->timestamps();
        });

        // Creating settings table for admin configurations
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skipped_customers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('booths');
        Schema::dropIfExists('settings');
    }
};
?>
