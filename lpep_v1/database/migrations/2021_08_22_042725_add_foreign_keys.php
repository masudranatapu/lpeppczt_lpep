<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('bank_list_id')->references('id')->on('bank_lists')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('asset_category_id')->references('id')->on('asset_categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('contact_payments', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('deposits', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('unit_id')->references('id')->on('units')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('quotation_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('quotation_id')->references('id')->on('quotations')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('return_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('salaries', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('employee_id')->references('id')->on('employees')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('sale_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('sale_id')->references('id')->on('sales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('sale_id')->references('id')->on('sales')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('withdraws', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign('accounts_bank_list_id_foreign');
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign('assets_account_id_foreign');
            $table->dropForeign('assets_asset_category_id_foreign');
        });
        Schema::table('contact_payments', function (Blueprint $table) {
            $table->dropForeign('contact_payments_contact_id_foreign');
        });
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign('deposits_account_id_foreign');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_account_id_foreign');
            $table->dropForeign('expenses_expense_type_id_foreign');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_brand_id_foreign');
            $table->dropForeign('products_category_id_foreign');
            $table->dropForeign('products_unit_id_foreign');
        });
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropForeign('purchase_products_product_id_foreign');
            $table->dropForeign('purchase_products_purchase_id_foreign');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign('purchases_contact_id_foreign');
        });
        Schema::table('quotation_products', function (Blueprint $table) {
            $table->dropForeign('quotation_products_product_id_foreign');
            $table->dropForeign('quotation_products_quotation_id_foreign');
        });
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign('quotations_contact_id_foreign');
            $table->dropForeign('quotations_user_id_foreign');
        });
        Schema::table('return_products', function (Blueprint $table) {
            $table->dropForeign('return_products_product_id_foreign');
            $table->dropForeign('return_products_sale_return_id_foreign');
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_role_id_foreign');
        });
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropForeign('salaries_account_id_foreign');
            $table->dropForeign('salaries_employee_id_foreign');
        });
        Schema::table('sale_products', function (Blueprint $table) {
            $table->dropForeign('sale_products_product_id_foreign');
            $table->dropForeign('sale_products_sale_id_foreign');
        });
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropForeign('sale_returns_contact_id_foreign');
            $table->dropForeign('sale_returns_sale_id_foreign');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('sales_contact_id_foreign');
            $table->dropForeign('sales_user_id_foreign');
        });
        Schema::table('sell_carts', function (Blueprint $table) {
            $table->dropForeign('sell_carts_product_id_foreign');
            $table->dropForeign('sell_carts_user_id_foreign');
        });
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign('supplier_payments_contact_id_foreign');
        });
        Schema::table('user_permissions', function (Blueprint $table) {
            $table->dropForeign('user_permissions_role_id_foreign');
            $table->dropForeign('user_permissions_user_id_foreign');
        });
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropForeign('withdraws_account_id_foreign');
        });
    }
}
