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
        // Bank Accounts
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
        
        // Mobile Money Accounts
        Schema::table('mobile_money_accounts', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
        
        // Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Cash Registers
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->change();
            $table->decimal('current_balance', 15, 2)->default(0)->change();
        });
        
        // Capitals
        Schema::table('capitals', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Accounting Entries
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Sales
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax', 15, 2)->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
            $table->decimal('paid', 15, 2)->change();
            $table->decimal('change', 15, 2)->change();
        });
        
        // Sale Items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Sale Returns
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->change();
        });
        
        // Sale Return Items
        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Customer Payments
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Purchase Orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax', 15, 2)->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Purchase Order Items
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Goods Received Notes
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->change();
        });
        
        // GRN Items
        Schema::table('grn_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Supplier Payments
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->nullable()->change();
            $table->decimal('selling_price', 15, 2)->change();
        });
        
        // Online Orders
        Schema::table('online_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('delivery_fee', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Online Order Items
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
        
        // Shares
        Schema::table('shares', function (Blueprint $table) {
            $table->decimal('share_price', 15, 2)->change();
            $table->decimal('total_amount', 15, 2)->change();
        });
        
        // Budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
