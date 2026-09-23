<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseSaleAdminIntegrationsTable extends Migration
{
    public function up()
    {
        Schema::table('lpep_warehouse_salesmen', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('warehouse_id');
            $table->foreign('user_id', 'wh_salesmen_user_fk')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('lpep_warehouse_sale_admin_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_sale_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('app_customer_id');
            $table->unsignedBigInteger('visit_info_id');
            $table->unsignedBigInteger('income_id');
            $table->decimal('income_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('warehouse_sale_id', 'wh_sale_sync_sale_fk')->references('id')->on('lpep_warehouse_sales')->onDelete('cascade');
            $table->foreign('user_id', 'wh_sale_sync_user_fk')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('app_customer_id', 'wh_sale_sync_customer_fk')->references('id')->on('app_customers')->onDelete('restrict');
            $table->foreign('visit_info_id', 'wh_sale_sync_visit_fk')->references('id')->on('visit_infos')->onDelete('restrict');
            $table->foreign('income_id', 'wh_sale_sync_income_fk')->references('id')->on('incomes')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lpep_warehouse_sale_admin_integrations');
        Schema::table('lpep_warehouse_salesmen', function (Blueprint $table) {
            $table->dropForeign('wh_salesmen_user_fk');
            $table->dropColumn('user_id');
        });
    }
}
