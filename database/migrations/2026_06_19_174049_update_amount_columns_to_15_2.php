<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'bank_accounts' => fn(Blueprint $t) => $t->decimal('balance', 15, 2)->default(0)->change(),
            'mobile_money_accounts' => fn(Blueprint $t) => $t->decimal('balance', 15, 2)->default(0)->change(),
            'expenses' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'incomes' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'cash_registers' => function(Blueprint $t) {
                $t->decimal('opening_balance', 15, 2)->default(0)->change();
                $t->decimal('current_balance', 15, 2)->default(0)->change();
            },
            'capitals' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'accounting_entries' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'sales' => function(Blueprint $t) {
                $t->decimal('subtotal', 15, 2)->change();
                $t->decimal('tax', 15, 2)->default(0)->change();
                $t->decimal('discount', 15, 2)->default(0)->change();
                $t->decimal('total', 15, 2)->change();
                $t->decimal('paid', 15, 2)->change();
                $t->decimal('change', 15, 2)->change();
            },
            'sale_items' => function(Blueprint $t) {
                $t->decimal('unit_price', 15, 2)->change();
                $t->decimal('discount', 15, 2)->default(0)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'sale_returns' => fn(Blueprint $t) => $t->decimal('total', 15, 2)->change(),
            'sale_return_items' => function(Blueprint $t) {
                $t->decimal('unit_price', 15, 2)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'customer_payments' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'purchase_orders' => function(Blueprint $t) {
                $t->decimal('subtotal', 15, 2)->change();
                $t->decimal('tax', 15, 2)->default(0)->change();
                $t->decimal('discount', 15, 2)->default(0)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'purchase_order_items' => function(Blueprint $t) {
                $t->decimal('unit_price', 15, 2)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'goods_received_notes' => fn(Blueprint $t) => $t->decimal('total', 15, 2)->change(),
            'grn_items' => function(Blueprint $t) {
                $t->decimal('unit_price', 15, 2)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'supplier_payments' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
            'products' => function(Blueprint $t) {
                $t->decimal('cost_price', 15, 2)->nullable()->change();
                $t->decimal('selling_price', 15, 2)->change();
            },
            'online_orders' => function(Blueprint $t) {
                $t->decimal('subtotal', 15, 2)->change();
                $t->decimal('delivery_fee', 15, 2)->default(0)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'online_order_items' => function(Blueprint $t) {
                $t->decimal('price', 15, 2)->change();
                $t->decimal('total', 15, 2)->change();
            },
            'shares' => function(Blueprint $t) {
                $t->decimal('share_price', 15, 2)->change();
                $t->decimal('total_amount', 15, 2)->change();
            },
            'budgets' => fn(Blueprint $t) => $t->decimal('amount', 15, 2)->change(),
        ];

        foreach ($tables as $table => $modifier) {
            if (Schema::hasTable($table)) {
                try { Schema::table($table, $modifier); } catch (\Exception $e) {}
            }
        }
    }

    public function down(): void {}
};
