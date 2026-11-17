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
    Schema::create('income_records', function (Blueprint $table) {
        $table->uuid("id")->primary();
        $table->date('income_date');
        $table->decimal('production_weight', 10, 2);
        $table->decimal('total_income', 15, 2);
        $table->foreignUuid('product_id')
            ->constrained('products')
            ->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('income_records');
}
};
