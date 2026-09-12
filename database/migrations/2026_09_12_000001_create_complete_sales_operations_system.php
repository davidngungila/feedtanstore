<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------------
        // 1. Extend users table for new roles if needed (no schema change, just handled in code)
        // -----------------------------------------------------------------

        // -----------------------------------------------------------------
        // 2. Customer Demands / Requests
        // -----------------------------------------------------------------
        if (!Schema::hasTable('customer_demands')) {
            Schema::create('customer_demands', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('customer_name')->nullable(); // if walk-in
                $table->string('customer_phone')->nullable();
                $table->string('product_requested');
                $table->string('product_requested_normalized')->nullable();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // if exists
                $table->integer('requested_quantity')->default(1);
                $table->date('request_date');
                $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->text('note')->nullable();
                $table->boolean('was_out_of_stock')->default(false);
                $table->enum('status', ['new','reviewing','planned','ordered','available','closed'])->default('new');
                $table->enum('channel', ['in_store','field_sales','online'])->default('in_store');
                $table->enum('source', ['cashier','field_sales','online_search','wishlist','abandoned_cart','other'])->default('cashier');
                $table->timestamps();
                $table->index(['status','branch_id','request_date']);
            });
        }

        // -----------------------------------------------------------------
        // 3. Transaction Issues
        // -----------------------------------------------------------------
        if (!Schema::hasTable('transaction_issues')) {
            Schema::create('transaction_issues', function (Blueprint $table) {
                $table->id();
                $table->string('issue_number')->unique();
                $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('online_order_id')->nullable()->constrained()->nullOnDelete();
                $table->string('transaction_type')->default('sale'); // sale, online_order
                $table->string('transaction_reference')->nullable();
                $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
                $table->dateTime('reported_at');
                $table->enum('issue_type', ['wrong_quantity','wrong_product','wrong_price','payment_problem','duplicate_transaction','customer_complaint','product_damaged','failed_transaction','receipt_problem','other'])->default('other');
                $table->text('description');
                $table->string('attachment')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['open','investigating','resolved','closed'])->default('open');
                $table->text('resolution')->nullable();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 4. Enhance Sale Returns (add approval workflow)
        // -----------------------------------------------------------------
        if (Schema::hasTable('sale_returns')) {
            Schema::table('sale_returns', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_returns', 'receipt_number')) $table->string('receipt_number')->nullable()->after('return_number');
                if (!Schema::hasColumn('sale_returns', 'approver_id')) $table->foreignId('approver_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('sale_returns', 'approval_status')) $table->enum('approval_status', ['pending','approved','rejected'])->default('pending')->after('reason');
                if (!Schema::hasColumn('sale_returns', 'refund_method')) $table->string('refund_method')->nullable()->after('approval_status');
                if (!Schema::hasColumn('sale_returns', 'refund_amount')) $table->decimal('refund_amount', 15, 2)->default(0)->after('refund_method');
                if (!Schema::hasColumn('sale_returns', 'approved_at')) $table->dateTime('approved_at')->nullable()->after('approver_id');
                if (!Schema::hasColumn('sale_returns', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('sale_id')->constrained()->nullOnDelete();
                if (!Schema::hasColumn('sale_returns', 'location_id')) $table->foreignId('location_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
                if (!Schema::hasColumn('sale_returns', 'stock_updated')) $table->boolean('stock_updated')->default(false)->after('refund_amount');
            });
        }
        if (Schema::hasTable('sale_return_items')) {
            Schema::table('sale_return_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_return_items', 'reason')) $table->string('reason')->nullable()->after('total');
                if (!Schema::hasColumn('sale_return_items', 'quantity_sold')) $table->integer('quantity_sold')->nullable()->after('quantity');
            });
        }

        // -----------------------------------------------------------------
        // 5. Stock Verification Sessions (Blind Audit)
        // -----------------------------------------------------------------
        if (!Schema::hasTable('stock_verification_sessions')) {
            Schema::create('stock_verification_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_number')->unique();
                $table->string('title')->nullable();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('status', ['draft','assigned','in_progress','submitted','under_review','approved','rejected','closed'])->default('draft');
                $table->foreignId('assigned_auditor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->dateTime('assigned_at')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->text('review_notes')->nullable();
                $table->integer('total_products')->default(0);
                $table->integer('counted_products')->default(0);
                $table->boolean('is_monthly_audit')->default(false);
                $table->date('audit_month')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('stock_verification_items')) {
            Schema::create('stock_verification_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->constrained('stock_verification_sessions')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('system_quantity')->default(0); // hidden from auditor
                $table->integer('physical_quantity')->nullable();
                $table->integer('variance_quantity')->nullable();
                $table->decimal('variance_percentage', 8, 2)->nullable();
                $table->enum('variance_type', ['shortage','surplus','matching'])->nullable();
                $table->enum('status', ['pending','counted'])->default('pending');
                $table->dateTime('counted_at')->nullable();
                $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->text('variance_reason')->nullable();
                $table->boolean('adjustment_created')->default(false);
                $table->timestamps();
                $table->unique(['session_id','product_id']);
            });
        }
        if (!Schema::hasTable('stock_verification_adjustments')) {
            Schema::create('stock_verification_adjustments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('session_id')->constrained('stock_verification_sessions')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity_before');
                $table->integer('quantity_after');
                $table->integer('quantity_change');
                $table->foreignId('stock_adjustment_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('stock_movement_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 6. Customer Store Ratings
        // -----------------------------------------------------------------
        if (!Schema::hasTable('customer_ratings')) {
            Schema::create('customer_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('online_order_id')->nullable()->constrained()->nullOnDelete();
                $table->string('transaction_reference')->nullable();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone')->nullable();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete(); // cashier who served
                $table->tinyInteger('rating')->comment('1-5');
                $table->tinyInteger('staff_service')->nullable();
                $table->tinyInteger('waiting_time')->nullable();
                $table->tinyInteger('product_availability')->nullable();
                $table->tinyInteger('price_rating')->nullable();
                $table->tinyInteger('cleanliness')->nullable();
                $table->tinyInteger('overall_experience')->nullable();
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 7. Cashier Service Times
        // -----------------------------------------------------------------
        if (!Schema::hasTable('cashier_service_times')) {
            Schema::create('cashier_service_times', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('online_order_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->dateTime('service_start_time');
                $table->dateTime('service_end_time')->nullable();
                $table->integer('duration_seconds')->nullable();
                $table->enum('status', ['active','completed','cancelled'])->default('active');
                $table->enum('channel', ['in_store','field_sales','online'])->default('in_store');
                $table->string('start_trigger')->default('new_sale'); // new_sale, first_scan
                $table->timestamps();
                $table->index(['cashier_id','service_start_time']);
            });
        }

        // -----------------------------------------------------------------
        // 8. Competitor Intelligence
        // -----------------------------------------------------------------
        if (!Schema::hasTable('competitor_intelligences')) {
            Schema::create('competitor_intelligences', function (Blueprint $table) {
                $table->id();
                $table->string('competitor_name');
                $table->string('competitor_location')->nullable();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('product_name');
                $table->string('product_name_normalized')->nullable();
                $table->decimal('competitor_price', 15, 2);
                $table->decimal('our_price', 15, 2);
                $table->decimal('price_difference', 15, 2)->nullable(); // our - competitor
                $table->decimal('price_difference_percent', 8, 2)->nullable();
                $table->string('availability')->nullable(); // in_stock, out_of_stock, limited
                $table->date('date_checked');
                $table->foreignId('sales_rep_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->text('notes')->nullable();
                $table->string('photo')->nullable();
                $table->enum('channel', ['field_sales','in_store','online'])->default('field_sales');
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 9. Offline Transactions / Sync Queue
        // -----------------------------------------------------------------
        if (!Schema::hasTable('offline_transactions')) {
            Schema::create('offline_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('local_transaction_id')->unique();
                $table->string('transaction_type')->default('sale'); // sale, return, payment
                $table->json('payload');
                $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('sync_status', ['pending','syncing','synced','failed','conflict'])->default('pending');
                $table->integer('sync_attempts')->default(0);
                $table->text('last_error')->nullable();
                $table->foreignId('synced_sale_id')->nullable()->constrained('sales')->nullOnDelete();
                $table->dateTime('offline_created_at');
                $table->dateTime('synced_at')->nullable();
                $table->string('device_info')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }

        // Enhance sales table for offline + channel + sync
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'sales_channel')) $table->enum('sales_channel', ['in_store','field_sales','online'])->default('in_store')->after('type');
                if (!Schema::hasColumn('sales', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('sales_channel')->constrained()->nullOnDelete();
                if (!Schema::hasColumn('sales', 'location_id')) $table->foreignId('location_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
                if (!Schema::hasColumn('sales', 'local_transaction_id')) $table->string('local_transaction_id')->nullable()->unique()->after('invoice_number');
                if (!Schema::hasColumn('sales', 'sync_status')) $table->enum('sync_status', ['synced','pending','failed'])->default('synced')->after('local_transaction_id');
                if (!Schema::hasColumn('sales', 'synced_at')) $table->dateTime('synced_at')->nullable()->after('sync_status');
                if (!Schema::hasColumn('sales', 'offline_created_at')) $table->dateTime('offline_created_at')->nullable()->after('synced_at');
                if (!Schema::hasColumn('sales', 'device_info')) $table->string('device_info')->nullable()->after('offline_created_at');
                if (!Schema::hasColumn('sales', 'gross_sales')) $table->decimal('gross_sales', 15, 2)->default(0)->after('total');
                if (!Schema::hasColumn('sales', 'cost_of_goods_sold')) $table->decimal('cost_of_goods_sold', 15, 2)->default(0)->after('gross_sales');
                if (!Schema::hasColumn('sales', 'gross_profit')) $table->decimal('gross_profit', 15, 2)->default(0)->after('cost_of_goods_sold');
                if (!Schema::hasColumn('sales', 'sales_rep_id')) $table->foreignId('sales_rep_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            });
        }

        // -----------------------------------------------------------------
        // 10. Enhance Online Orders: referral / campaign / payment-first fulfillment
        // -----------------------------------------------------------------
        if (Schema::hasTable('online_orders')) {
            Schema::table('online_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('online_orders', 'reference_code')) $table->string('reference_code')->nullable()->after('order_number');
                if (!Schema::hasColumn('online_orders', 'customer_reference_code')) $table->string('customer_reference_code')->nullable()->after('reference_code');
                if (!Schema::hasColumn('online_orders', 'referral_code')) $table->string('referral_code')->nullable()->after('customer_reference_code');
                if (!Schema::hasColumn('online_orders', 'campaign_code')) $table->string('campaign_code')->nullable()->after('referral_code');
                if (!Schema::hasColumn('online_orders', 'sales_rep_code')) $table->string('sales_rep_code')->nullable()->after('campaign_code');
                if (!Schema::hasColumn('online_orders', 'sales_rep_id')) $table->foreignId('sales_rep_id')->nullable()->after('sales_rep_code')->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('online_orders', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('sales_rep_id')->constrained()->nullOnDelete();
                if (!Schema::hasColumn('online_orders', 'fulfillment_status')) $table->enum('fulfillment_status', ['new','confirmed','processing','ready_for_pickup','out_for_delivery','delivered','cancelled','awaiting_payment','payment_reminder'])->nullable()->after('status');
                if (!Schema::hasColumn('online_orders', 'payment_authorized_by')) $table->foreignId('payment_authorized_by')->nullable()->after('payment_status')->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('online_orders', 'fulfillment_authorized_by')) $table->foreignId('fulfillment_authorized_by')->nullable()->after('fulfillment_status')->constrained('users')->nullOnDelete();
                if (!Schema::hasColumn('online_orders', 'is_paid_override')) $table->boolean('is_paid_override')->default(false)->after('is_processed');
            });
            // Expand payment_status enum to include partially_paid, refunded if sqlite we just keep string; for mysql we alter column to string
            // We'll handle via DBAL alter
            try {
                Schema::table('online_orders', function (Blueprint $table) {
                    $table->string('payment_status_new')->nullable()->after('payment_status');
                });
                // migrate data not needed here; just ensure column can hold new values (in sqlite it's already string-like)
                // We'll drop the temp column and change original if using mysql
                Schema::table('online_orders', function (Blueprint $table) {
                    $table->dropColumn('payment_status_new');
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }

        // -----------------------------------------------------------------
        // 11. Enhance Action Logs / Audit Trail
        // -----------------------------------------------------------------
        if (Schema::hasTable('action_logs')) {
            Schema::table('action_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('action_logs', 'module')) $table->string('module')->nullable()->after('action');
                if (!Schema::hasColumn('action_logs', 'record_type')) $table->string('record_type')->nullable()->after('module');
                if (!Schema::hasColumn('action_logs', 'record_id')) $table->unsignedBigInteger('record_id')->nullable()->after('record_type');
                if (!Schema::hasColumn('action_logs', 'old_values')) $table->json('old_values')->nullable()->after('record_id');
                if (!Schema::hasColumn('action_logs', 'new_values')) $table->json('new_values')->nullable()->after('old_values');
                if (!Schema::hasColumn('action_logs', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('new_values')->constrained()->nullOnDelete();
            });
        }

        // -----------------------------------------------------------------
        // 12. Field Sales Orders (for mobile app sales that are orders not immediate sale)
        // -----------------------------------------------------------------
        if (!Schema::hasTable('field_sales_orders')) {
            Schema::create('field_sales_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->string('local_transaction_id')->nullable()->unique();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('customer_name');
                $table->string('customer_phone')->nullable();
                $table->foreignId('sales_rep_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->enum('payment_status', ['pending','paid','partially_paid','failed','refunded'])->default('pending');
                $table->enum('status', ['pending','confirmed','processing','delivered','cancelled'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('outstanding_amount', 15, 2)->default(0);
                $table->decimal('cost_of_goods_sold', 15, 2)->default(0);
                $table->decimal('gross_profit', 15, 2)->default(0);
                $table->enum('sync_status', ['synced','pending','failed'])->default('synced');
                $table->dateTime('synced_at')->nullable();
                $table->dateTime('offline_created_at')->nullable();
                $table->string('device_info')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('field_sales_order_items')) {
            Schema::create('field_sales_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('field_sales_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity');
                $table->decimal('unit_price', 15, 2);
                $table->decimal('cost_price', 15, 2)->default(0);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('total', 15, 2);
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 13. Wishlist / Abandoned Cart / Online Demand Signals
        // -----------------------------------------------------------------
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('session_id')->nullable();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('abandoned_carts')) {
            Schema::create('abandoned_carts', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->nullable();
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->json('cart_data');
                $table->decimal('cart_total', 15, 2)->default(0);
                $table->dateTime('abandoned_at');
                $table->boolean('is_recovered')->default(false);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('online_search_logs')) {
            Schema::create('online_search_logs', function (Blueprint $table) {
                $table->id();
                $table->string('search_term');
                $table->string('search_term_normalized')->nullable();
                $table->integer('results_count')->default(0);
                $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('session_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->boolean('was_found')->default(true);
                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 14. Revenue Snapshots for reporting
        // -----------------------------------------------------------------
        if (!Schema::hasTable('revenue_snapshots')) {
            Schema::create('revenue_snapshots', function (Blueprint $table) {
                $table->id();
                $table->date('snapshot_date');
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('sales_channel', ['in_store','field_sales','online','all'])->default('all');
                $table->decimal('gross_sales', 15, 2)->default(0);
                $table->decimal('discounts', 15, 2)->default(0);
                $table->decimal('tax', 15, 2)->default(0);
                $table->decimal('net_sales', 15, 2)->default(0);
                $table->decimal('cost_of_goods_sold', 15, 2)->default(0);
                $table->decimal('gross_profit', 15, 2)->default(0);
                $table->decimal('payment_amount', 15, 2)->default(0);
                $table->decimal('outstanding_amount', 15, 2)->default(0);
                $table->decimal('refunds', 15, 2)->default(0);
                $table->decimal('final_revenue', 15, 2)->default(0);
                $table->integer('orders_count')->default(0);
                $table->timestamps();
                $table->unique(['snapshot_date','branch_id','sales_channel']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_snapshots');
        Schema::dropIfExists('online_search_logs');
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('field_sales_order_items');
        Schema::dropIfExists('field_sales_orders');
        Schema::dropIfExists('offline_transactions');
        Schema::dropIfExists('competitor_intelligences');
        Schema::dropIfExists('cashier_service_times');
        Schema::dropIfExists('customer_ratings');
        Schema::dropIfExists('stock_verification_adjustments');
        Schema::dropIfExists('stock_verification_items');
        Schema::dropIfExists('stock_verification_sessions');
        Schema::dropIfExists('transaction_issues');
        Schema::dropIfExists('customer_demands');
        // Note: we don't rollback column additions for safety
    }
};
