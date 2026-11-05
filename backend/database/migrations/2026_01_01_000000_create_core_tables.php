<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('role')->default('cashier')->index();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->string('product_code', 50)->unique();
            $t->string('product_name')->index();
            $t->integer('stock')->default(0);
            $t->decimal('price', 12, 2);
            $t->integer('low_stock_threshold')->default(5);
            $t->timestamps();
            $t->softDeletes();
            $t->index(['deleted_at', 'product_name']);
        });

        Schema::create('customers', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('parent_customer_id')->nullable()->constrained('customers', 'id');
            $t->string('customer_name')->index();
            $t->text('customer_address');
            $t->string('gender', 10);
            $t->date('birth_date')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_number', 30)->unique();
            $t->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('created_by')->constrained('users');
            $t->string('status')->index();
            $t->decimal('total_amount', 14, 2);
            $t->timestamp('order_date')->index();
            $t->timestamp('paid_at')->nullable();
            $t->timestamp('shipped_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamp('cancelled_at')->nullable();
            $t->string('cancel_reason')->nullable();
            $t->timestamps();
            $t->index(['status', 'order_date']);
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained();
            $t->integer('quantity');
            $t->decimal('unit_price', 12, 2);
            $t->decimal('subtotal', 14, 2);
            $t->index(['product_id', 'order_id']);
        });

        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained();
            $t->string('type');
            $t->integer('quantity_change');
            $t->integer('stock_after');
            $t->string('reference_type')->nullable();
            $t->string('reference_id')->nullable();
            $t->text('note')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users');
            $t->timestamp('created_at');
            $t->index(['product_id', 'created_at']);
        });

        Schema::create('idempotency_keys', function (Blueprint $t) {
            $t->id();
            $t->string('key', 36)->unique();
            $t->char('fingerprint', 64);
            $t->string('status');
            $t->unsignedSmallInteger('attempts')->default(0);
        });

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $t) {
                $t->id();
                $t->morphs('tokenable');
                $t->string('name');
                $t->string('token', 64)->unique();
                $t->text('abilities')->nullable();
                $t->timestamp('last_used_at')->nullable();
                $t->timestamp('expires_at')->nullable()->index();
                $t->timestamps();
            });
        }
    }

    public function down(): void {
        foreach (['personal_access_tokens', 'idempotency_keys', 'stock_movements', 'order_items', 'orders', 'customers', 'products', 'users'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
