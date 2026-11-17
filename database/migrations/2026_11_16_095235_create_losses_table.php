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
    Schema::create('losses', function (Blueprint $table) {
        $table->uuid("id")->primary();
        $table->foreignUuid('product_id')
            ->constrained('products')
            ->cascadeOnDelete();
        $table->string('cause');
        $table->date('loss_date');
        $table->decimal('damaged_weight_kg', 10, 2);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('losses');
}
};
