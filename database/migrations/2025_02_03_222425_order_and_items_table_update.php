<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_discount', 10, 2)->default(0)->after('total');
            $table->decimal('discounted_total', 10, 2)->default(0)->after('total_discount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('total');
            $table->decimal('discounted_total', 10, 2)->nullable()->after('discount_amount');
        });

        Schema::create('order_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->string('discount_reason');
            $table->decimal('subtotal', 8, 2);
            $table->decimal('discount_amount', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_discount', 'discounted_total']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['discount_amount','discounted_total']);
        });

        Schema::dropIfExists('order_discounts');
    }
};
