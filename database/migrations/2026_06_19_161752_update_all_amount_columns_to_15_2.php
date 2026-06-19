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
        // Update bank accounts
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
        
        // Update mobile money accounts
        Schema::table('mobile_money_accounts', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->change();
        });
        
        // Update expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Update incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Update cash registers
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->change();
            $table->decimal('current_balance', 15, 2)->default(0)->change();
        });
        
        // Update journal entry items
        Schema::table('journal_entry_items', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Update budgets (if needed)
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Update assets (if needed)
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('value', 15, 2)->change();
        });
        
        // Update supplier payments
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
        
        // Update customer payments
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert bank accounts
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0)->change();
        });
        
        // Revert mobile money accounts
        Schema::table('mobile_money_accounts', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0)->change();
        });
        
        // Revert expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // Revert incomes
        Schema::table('incomes', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // Revert cash registers
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->decimal('opening_balance', 10, 2)->default(0)->change();
            $table->decimal('current_balance', 10, 2)->default(0)->change();
        });
        
        // Revert journal entry items
        Schema::table('journal_entry_items', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // Revert budgets (if needed)
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // Revert assets (if needed)
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('value', 10, 2)->change();
        });
        
        // Revert supplier payments
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
        
        // Revert customer payments
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
