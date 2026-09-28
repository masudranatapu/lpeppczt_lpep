<?php

use App\Http\Controllers\CowFeedController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\VisitInfoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\App\CalfController;
use App\Http\Controllers\App\FarmController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\VatGroupController;
use App\Http\Controllers\App\CattleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReturnSaleController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AdminAgentSaleListController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\AdminDepositController;
use App\Http\Controllers\AgentStockTransferController;
use App\Http\Controllers\App\MAccountController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\App\CattleFoodController;
use App\Http\Controllers\App\HealthInfoController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\App\AppCustomerController;
use App\Http\Controllers\App\CattleBreedController;
use App\Http\Controllers\App\CattleGroupController;
use App\Http\Controllers\App\SeedCompanyController;
use App\Http\Controllers\App\AppDataReportController;
use App\Http\Controllers\App\CattleDiseaseController;
use App\Http\Controllers\App\CattleVaccineController;
use App\Http\Controllers\App\InsuranceTypeController;
use App\Http\Controllers\App\DiseaseHistoryController;

use App\Http\Controllers\App\BioEnergyController;
use App\Http\Controllers\App\RenewableEnergyController;






use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PurchaseReturnTypeController;
use App\Http\Controllers\App\CalfBirthProblemController;
use App\Http\Controllers\App\InsuranceCompanyController;
use App\Http\Controllers\App\AdminAgentTransactionController;
use App\Http\Controllers\App\AgentCustomerTransactionController;
use App\Http\Controllers\Frontendcontroller;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\AgentToAgentTransferController;
use App\Http\Controllers\IncomeTypeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseSalesmanController;
use App\Http\Controllers\WarehousePurchaseController;
use App\Http\Controllers\WarehousePortalController;
use App\Http\Controllers\WarehousePortalReportController;
use App\Http\Controllers\WarehouseStockTransferController;
use App\Http\Controllers\WarehouseSalesmanAssignmentController;
use App\Http\Controllers\WarehouseSalesmanReturnController;
use App\Http\Controllers\Warehouse\Auth\LoginController as WarehouseLoginController;

use Illuminate\Support\Facades\Artisan;


// Ensure the name matches



// OR simply


Route::get('/', function () {
    return view('frontend.index');
})->name('frontend.index');

Route::get('/about-us', [Frontendcontroller::class, 'aboutUs'])->name('about.us');
Route::get('/contact-us', [Frontendcontroller::class, 'contact'])->name('contact.us');
Route::get('/mission', [Frontendcontroller::class, 'mission'])->name('mission');
Route::get('/vision', [Frontendcontroller::class, 'vision'])->name('vision');
Route::get('/service', [Frontendcontroller::class, 'service'])->name('service');

Auth::routes(['register' => false]);
Route::get("/login", [LoginController::class, 'loginpage'])->name('login')->middleware('checkuser');
// Keep the form submission route separate from the GET login page route.
// The login view posts to this named route.
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('guest');
Route::post("/reset-password", [ResetPasswordController::class, "resetPassword"])->name("reset.password");
Route::post("/check-otp", [ResetPasswordController::class, "checkOtp"])->name("otp-check");
Route::post("/change-password", [ResetPasswordController::class, 'changePassword'])->name("change-password");

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/lang/change', [LangController::class, 'change'])->name('changeLang');

Route::middleware(['auth'])->group(function () {
    //todaySummary
    Route::get('/today/summary', [HomeController::class, 'todaySummary'])->name('today.summary');
    // Warehouse management
    Route::get('warehouses/stock', [WarehouseController::class, 'stockOverview'])->name('warehouses.stock');
    Route::get('warehouses/stock/print', [WarehouseController::class, 'stockOverviewPrint'])->name('warehouses.stock.print');
    Route::get('warehouses/stock/pdf', [WarehouseController::class, 'stockOverviewPdf'])->name('warehouses.stock.pdf');
    Route::get('warehouses/stock/excel', [WarehouseController::class, 'stockOverviewExcel'])->name('warehouses.stock.excel');
    Route::get('warehouses/area-manager-daily', [WarehouseController::class, 'areaManagerDaily'])->name('warehouses.area-manager-daily');
    Route::get('warehouses/area-manager-daily/pdf', [WarehouseController::class, 'areaManagerDailyPdf'])->name('warehouses.area-manager-daily.pdf');
    Route::get('warehouses/area-manager-daily/excel', [WarehouseController::class, 'areaManagerDailyExcel'])->name('warehouses.area-manager-daily.excel');
    Route::resource('warehouses', WarehouseController::class);
    Route::get('warehouse-salesmen/{warehouse_salesman}/reports', [WarehouseSalesmanController::class, 'salesReport'])
        ->name('warehouse-salesmen.reports');
    Route::get('warehouse-salesmen/{warehouse_salesman}/reports/print', [WarehouseSalesmanController::class, 'salesReportPrint'])
        ->name('warehouse-salesmen.reports.print');
    Route::get('warehouse-salesmen/{warehouse_salesman}/reports/pdf', [WarehouseSalesmanController::class, 'salesReportPdf'])
        ->name('warehouse-salesmen.reports.pdf');
    Route::get('warehouse-salesmen/{warehouse_salesman}/reports/excel', [WarehouseSalesmanController::class, 'salesReportExcel'])
        ->name('warehouse-salesmen.reports.excel');
    Route::resource('warehouse-salesmen', WarehouseSalesmanController::class)->parameters([
        'warehouse-salesmen' => 'warehouse_salesman',
    ]);
    Route::get('warehouse-purchases/{warehouse_purchase}/invoice', [WarehousePurchaseController::class, 'invoice'])
        ->name('warehouse-purchases.invoice');
    Route::post('warehouse-purchases/{warehouse_purchase}/payments', [WarehousePurchaseController::class, 'payDue'])
        ->name('warehouse-purchases.payments.store');
    Route::resource('warehouse-purchases', WarehousePurchaseController::class)->only([
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ]);
    Route::get('admin-stock-summary', [WarehouseStockTransferController::class, 'adminStockSummary'])
        ->name('admin-stock-summary.index');
    Route::resource('warehouse-stock-transfers', WarehouseStockTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::delete('warehouse-stock-transfers/{warehouse_stock_transfer}', [WarehouseStockTransferController::class, 'destroy'])->name('warehouse-stock-transfers.destroy');
    Route::get('warehouse-salesman-assignments/transfer-list', [WarehouseSalesmanAssignmentController::class, 'transferList'])->name('warehouse-salesman-assignments.transfer-list');
    Route::resource('warehouse-salesman-assignments', WarehouseSalesmanAssignmentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('warehouse-salesman-returns', [WarehouseSalesmanReturnController::class, 'adminIndex'])->name('warehouse-salesman-returns.index');
    Route::get('warehouse-salesman-returns/{warehouse_salesman_return}', [WarehouseSalesmanReturnController::class, 'adminShow'])->name('warehouse-salesman-returns.show');

    // CustomerGroupController Routes
    Route::resource('customer-group', CustomerGroupController::class);
    Route::get('/all/customer/json', [CustomerGroupController::class, 'allCustomerGroup'])->name('allcustomer.json');
    Route::post('/customer-group/update', [CustomerGroupController::class, 'groupUpdate'])->name('customer-group.updateded');
    Route::post('/customer-group/delete', [CustomerGroupController::class, 'delete'])->name('customer-group.delete');

    // ContactController Routes
    Route::get('/contact/ajax', [ContactController::class, 'contactAjax']);
    Route::get('/contact', [ContactController::class, 'contact']);
    Route::get('/contact/supplier/create', [ContactController::class, 'createSupplier'])->name('contact.supplier.create');
    Route::get('/contact/supplier/json', [ContactController::class, 'supplierJson'])->name('contact.supplier.json');
    Route::get('/contact/customer/create', [ContactController::class, 'createCustomer'])->name('contact.customer.create');
    Route::get('/contact/customer/json', [ContactController::class, 'customerJson'])->name('contact.customer.json');
    Route::get('/contact/list/json/{type}', [ContactController::class, 'contactListJson'])->name('contact.list.json');
    Route::post('contact/store', [ContactController::class, 'contactStore'])->name('contact.store');
    Route::post('contact/update', [ContactController::class, 'contactUpdate'])->name('contact.update');
    Route::get('/contact/edit/{id}', [ContactController::class, 'contactEdit'])->name('contact.edit');
    Route::post('/contact/status/update', [ContactController::class, 'statusUpdate'])->name('status.update');
    Route::post('/contact/group/check', [ContactController::class, 'contactGroupCheck'])->name('contact.group.check');
    Route::post('/contact/group/check-by-agent', [ContactController::class, 'contactGroupCheckAgent'])->name('contact.group.check-by-agent');
    Route::post('/contact/pay', [ContactController::class, 'contactPay'])->name('contact.pay');
    Route::post('/contact/pay/store', [ContactController::class, 'paymentStore'])->name('contact.payment.store');
    Route::get("/contact-business-wise/{business_id}", [ContactController::class, 'businessWiseContact'])->name("business-wise-contact");
    Route::post("/customer/delete/{id}", [ContactController::class, 'customerDelete'])->name("customer.delete");

    // UnitController Routes
    Route::resource('unit', UnitController::class);
    Route::get('/all/unit/json', [UnitController::class, 'allUnit'])->name('all.unit.json');
    Route::post('/unit/updated', [UnitController::class, 'unitUpdate'])->name('unit.updated');
    Route::post('/unit/delete', [UnitController::class, 'unitDeleted'])->name('unit.delete');

    // CategoryController Routes
    Route::resource('category', CategoryController::class);
    Route::get('/all/category/json', [CategoryController::class, 'allCategory'])->name('all.category.json');
    Route::post('/category/updated', [CategoryController::class, 'categoryUpdate'])->name('category.updated');
    Route::post('/category/delete', [CategoryController::class, 'categoryStatus'])->name('category.status');

    // CategoryController Routes
    Route::resource('brand', BrandController::class);
    Route::get('/all/brand/json', [BrandController::class, 'allBrand'])->name('all.brand.json');
    Route::post('/brand/updated', [BrandController::class, 'brandUpdate'])->name('brand.updated');
    Route::post('/brand/delete', [BrandController::class, 'brandStatus'])->name('brand.status');

    // ProductController Routes
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::get('/product/{id}/active', [ProductController::class, 'active'])->name('product.active');
    Route::get('/product/{id}/deactive', [ProductController::class, 'deactive'])->name('product.deactive');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::post('/product/update', [ProductController::class, 'update'])->name('product.update');
    Route::post('/product/validation', [ProductController::class, 'customValidation'])->name('product.validation');
    Route::get('/product/{id}/destroy', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/product/{id}/view', [ProductController::class, 'view'])->name('product.view');
    Route::get('/product/json', [ProductController::class, 'productJson'])->name('product.json');

    // BarcodeGenerate
    Route::get('/product/barcode', [BarcodeController::class, 'barcode'])->name('product.barcode');
    Route::post('/product/barcode/print', [BarcodeController::class, 'printBarcode'])->name('barcode.print');
    Route::get('/product/barcode/print', [BarcodeController::class, 'printBarcodeBack']);
    Route::get('/label/print', [BarcodeController::class, "labelPrint"])->name('print.label');
    Route::get('/test', [BarcodeController::class, 'test']);

    Route::get('/autocomplete/purchase', [BarcodeController::class, 'autocompletePurchase']);
    Route::get('/autocomplete/barcode', [BarcodeController::class, 'autocompleteBarcode']);
    Route::get('/autocomplete/sale', [BarcodeController::class, 'autocompleteSale']);
    Route::get('/autocomplete/transfer', [BarcodeController::class, 'autocompleteTransfer'])->name('autocomplete.transfer');
    Route::get('/autocomplete/agent-transfer', [BarcodeController::class, 'autocompleteAgentTransfer'])->name('autocomplete.agent-transfer');

    // PurchaseController Routes here
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('/purchase/product', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::get('/purchase/batch-id-exists', [PurchaseController::class, 'doesBatchIdExist'])->name('purchase.does-batch-id-exist');
    Route::post('/purchase/serach', [PurchaseController::class, 'getProduct'])->name('purchase.search'); // deprecated
    Route::post('purchase/store', [PurchaseController::class, 'store'])->name("purchase.store");
    Route::get('/purchase/{id}/details', [PurchaseController::class, 'show']);
    Route::get('/purchase/{id}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::get('/purchase/{id}/delete', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
    Route::post('/purchase/update', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::get('/purchase/{id}/invoice', [PurchaseController::class, 'invoice'])->name('purchase.invoice');
    Route::post('/puchase/payment/account', [PurchaseController::class, 'account'])->name('purchase.payment.account');

    // StockController Routes here
    Route::get('/stock', [StockController::class, 'index'])->name('stock');

    // VatGroupController Routes
    Route::get('/vat-sd/group', [VatGroupController::class, 'index'])->name('vat-group');
    Route::get('/vat-sd/group/create', [VatGroupController::class, 'create'])->name('vat-sd.create');
    Route::get('/vat-sd/group/{id}/edit', [VatGroupController::class, 'edit'])->name('vat-sd.edit');
    Route::post('/vat-sd/group/store', [VatGroupController::class, 'store'])->name('vat-sd.store');
    Route::post('/vat-sd/group/update', [VatGroupController::class, 'update'])->name('vat-sd.update');
    Route::post('/vat-sd/group/delete', [VatGroupController::class, 'delete'])->name('vat-group.delete');
    Route::get('/vat-sd/group/all/json', [VatGroupController::class, 'vatGroupAll'])->name('vat-group.all');

    // SaleController Routes here
    Route::get('/sale', [SaleController::class, 'index'])->name('sale');
    Route::get('/pos', [SaleController::class, 'pos'])->name('pos');
    Route::post('/sale/cart', [SaleController::class, 'cartAdd'])->name('sale.cart');
    Route::get('/sale/clear', [SaleController::class, 'cartClear'])->name('pos.clear');
    Route::get('/sale/{sale_id}/view', [SaleController::class, 'view'])->name('sale.view');
    Route::get('/sale/{sale_id}/delete', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::post('/update-due-amount', [SaleController::class, 'customerDueClear'])->name('sale.customer.due.clear');

    Route::get('/sale/cart/changeBatchId', [SaleController::class, 'cartChangeBatchId'])->name('cart.change-batch-id');
    Route::post('/sale/cart/remove', [SaleController::class, 'cartRemove'])->name('cart.remove');
    Route::post('/sale/cart/remove', [SaleController::class, 'cartRemove'])->name('cart.remove');
    Route::post('/sale/cart/qty/increment', [SaleController::class, 'cartIncrement'])->name('cart.increment');
    Route::post('/sale/cart/qty/update', [SaleController::class, 'cartQtyUpdate'])->name('cart.qty.update');
    Route::post('/sale/cart/qty/decrement', [SaleController::class, 'cartDecrement'])->name('cart.decrement');
    Route::get('/sale/cart/list', [SaleController::class, 'cartList'])->name('cart.list');

    Route::post('/sale/store', [SaleController::class, 'store'])->name('sale.store');
    Route::get('/pos/{id}/invoice', [SaleController::class, 'posInvoice'])->name('pos.invoice');
    Route::post('/filter/produt/category', [SaleController::class, 'filterProductCategory'])->name('filter.product.category');
    Route::post('/filter/produt/brand', [SaleController::class, 'filterProductBrand'])->name('filter.product.brand');
    Route::post('/product/stock/check', [SaleController::class, 'checkProductStock'])->name('check.product.stock');
    Route::get('/sale/{sale_id}/invoice', [SaleController::class, 'invoice'])->name('sale.invoice');
    Route::post('/sale/payment/account', [SaleController::class, 'account'])->name('sale.payment.account');
    Route::post('/pos/price/change', [SaleController::class, 'cartChangePrice'])->name('sale.change.price');

    // ReturnSaleController Routes
    Route::get('/sale/return', [ReturnSaleController::class, 'saleInReturn'])->name('sale.in.return');
    Route::post('/sale/return', [ReturnSaleController::class, 'saleInReturnPost'])->name('sale.in.return.post');
    Route::get('/sale/{sale_id}/return', [ReturnSaleController::class, 'create'])->name('sale.return');
    Route::get('/sale/return/list', [ReturnSaleController::class, 'index'])->name('return.sale.index');
    Route::post('retur/sale', [ReturnSaleController::class, 'store'])->name('return.sale.store');
    Route::post('retur/sale/update', [ReturnSaleController::class, 'update'])->name('return.sale.update');
    Route::get('/sale/return/{id}/details', [ReturnSaleController::class, 'returnDetails']);
    Route::get('/sale/return/{id}/edit', [ReturnSaleController::class, 'edit'])->name('sale.return.edit');
    Route::get('/sale/return/{id}/delete', [ReturnSaleController::class, 'destroy'])->name('sale.return.destroy');
    Route::get('/sale/return/{id}/invoice', [ReturnSaleController::class, 'invoice'])->name('sale.return.invoice');
    Route::post('/sale/return/pyment/account', [ReturnSaleController::class, 'account'])->name('returnsale.payment.account');

    // Eexpanse type Routes
    Route::get('/expense-type', [ExpenseTypeController::class, 'index'])->name('expense.type');
    Route::post('/expense-type/store', [ExpenseTypeController::class, 'store'])->name('expense.type.store');
    Route::get('/expense-type/{id}/edit', [ExpenseTypeController::class, 'edit'])->name('expense.type.edit');
    Route::post('/expense-type/update', [ExpenseTypeController::class, 'update'])->name('expense.type.update');
    Route::get('/expense-type/{id}/delete', [ExpenseTypeController::class, 'destroy'])->name('expense.type.destroy');



    // Expense Routes
    Route::get('/expense', [ExpenseController::class, 'index'])->name('expense');
    Route::get('/expense/all/json', [ExpenseController::class, 'allExpenseJson'])->name('expense.all');
    Route::get('/expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('/expense/store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/expense/{id}/edit', [ExpenseController::class, 'edit'])->name('expense.edit');
    Route::post('/expense/update', [ExpenseController::class, 'update'])->name('expense.update');
    Route::get('/expense/{id}/delete', [ExpenseController::class, 'destroy'])->name('expense.destroy');
    Route::post('/expnese/payment/account', [ExpenseController::class, 'account'])->name('expense.payment.account');

    // ----------------income and income type routes ----------------------
    Route::resources([
        'income' => IncomeController::class,
        'incomeType' => IncomeTypeController::class
    ]);
    Route::get('/income-type/print/{incomeType}', [IncomeTypeController::class, 'print'])->name('incomeType.print');
    Route::get('/income-type/sales', [IncomeTypeController::class, 'icomeTypeHistory'])->name('incomeType.report');
    Route::get('/income/all/json', [IncomeController::class, 'allIncomeJson'])->name('income.all');
    Route::get('/history', [IncomeController::class, 'history'])->name('income.history');


    // Employee Routes
    Route::resource('/employee', EmployeeController::class);
    Route::get('/employee/{id}/delete', [EmployeeController::class, 'destroy'])->name('employee.delete');

    // Salary Routes
    Route::get('/all/salary', [SalaryController::class, 'allSalary'])->name('all.salary');
    Route::get("/pay/{employe_id}/salary", [SalaryController::class, 'paySalary'])->name('salary.pay');
    Route::post("/pay/salary", [SalaryController::class, 'paidSalary'])->name('salary.paid');
    Route::get('salary/{month}', [SalaryController::class, 'salaryMonth'])->name('salary.month');
    Route::get('/employee/salary/{employee_id}/pay', [SalaryController::class, 'employeeSalaryPay'])->name('employee.salary.pay');

    // UserRole Routes
    Route::get('/user/role', [RoleController::class, "index"])->name('role.index');
    Route::get('/user/role/create', [RoleController::class, "create"])->name('role.create');
    Route::post("/user/role/store", [RoleController::class, 'store'])->name('role.store');
    Route::get('/user/role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
    Route::post("/user/role/update", [RoleController::class, 'update'])->name('role.update');
    Route::get('/user/role/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');

    // User Routes
    Route::get('/user/list', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/{id}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get("/user-business-wise/{business_id}", [UserController::class, 'businessWiseUser'])->name("business-wise-report");

    // ReportController Routes
    Route::get('/stock/report', [ReportController::class, 'stockReport'])->name('report.stock');
    Route::get('/stock/report-export', [ReportController::class, 'stockReportExport'])->name('report.stock.export');


    Route::get('/product/report', [ReportController::class, 'productReport'])->name('report.product');
    Route::get('/report/category', [ReportController::class, 'categoryReport'])->name('report.category');
    Route::get('/report/sale', [ReportController::class, 'saleReport'])->name('report.sale');
    Route::get('/report/agent/sale', [ReportController::class, 'agentSaleReport'])->name('agent.sale.report');
    Route::get('/report/purchase', [ReportController::class, 'purchaseReport'])->name('report.purchase');
    Route::get('/report/daily/sale', [ReportController::class, 'dailySaleReport'])->name('report.dailySaleReport');
    Route::get('/report/monthly/sale', [ReportController::class, 'monthlySaleReport'])->name('report.monthlySaleReport');
    Route::get('/report/customer', [ReportController::class, 'customerReport'])->name('report.customer');
    Route::get('/report/supplier', [ReportController::class, 'supplierReport'])->name('report.supplier');
    Route::get('/report/profit/loss', [ReportController::class, 'profitLoss'])->name('report.profitLoss');
    Route::get('/report/sold/product', [ReportController::class, 'soldProduct'])->name('report.sold.product');
    Route::get('/report/due/sale', [ReportController::class, 'dueSaleReport'])->name('report.due.sale');
    Route::get('/report/lsp/due', [ReportController::class, 'lspDueReport'])->name('report.lsp.due');
    Route::get('/report/lsp/sale', [ReportController::class, 'lspSaleReport'])->name('report.lsp.sale');
    Route::get('/report/lsp/sale/print', [ReportController::class, 'lspSaleReportPrint'])->name('report.lsp.sale.print');
    Route::get('/report/lsp/sale/pdf', [ReportController::class, 'lspSaleReportPdf'])->name('report.lsp.sale.pdf');
    Route::get('/report/lsp/sale/excel', [ReportController::class, 'lspSaleReportExcel'])->name('report.lsp.sale.excel');
    Route::get('/report/lsp/sale/invoice/{warehouse_sale}', [WarehousePortalController::class, 'adminInvoice'])->name('report.lsp.sale.invoice');

    // Purchase Damage Type Routes
    Route::get("/purchase/damage-type/{id}/delete", [PurchaseReturnTypeController::class, "destroy"])->name("purchase.damage-type.delete");
    Route::resource('/purchase/damage-type', PurchaseReturnTypeController::class);

    // Purchase Return Routes
    Route::get("/purchase/{purchase_id}/return", [PurchaseReturnController::class, 'purchaseReturn'])->name("purchase.return");
    Route::get("/purchase-return", [PurchaseReturnController::class, 'index'])->name("purchase-return.index");
    Route::post("/purchase-return", [PurchaseReturnController::class, 'store'])->name("purchase-return.store");
    Route::get("/purchase-return/{id}/show", [PurchaseReturnController::class, 'show'])->name("purchase-return.view");
    Route::get("/purchase-return/{id}/edit", [PurchaseReturnController::class, 'edit'])->name("purchase-return.edit");
    Route::post("/purchase-return/{id}/update", [PurchaseReturnController::class, 'update'])->name("purchase-return.update");
    Route::post("/purchase-return/delete", [PurchaseReturnController::class, 'destroy'])->name("purchase-return.destroy");

    // AccountController Routes
    Route::get('/account/salary/report', [AccountController::class, 'salaryReport'])->name('account.salary.report');
    Route::get('/account/receive/report', [AccountController::class, 'receiveAmount'])->name('account.receive.report');
    Route::get('/account/received/report', [AccountController::class, 'receivedAmount'])->name('account.received.report');
    Route::get('/account/receiveable/report', [AccountController::class, 'receiveableAmount'])->name('account.receiveable.report');
    Route::get('/account/payment/report', [AccountController::class, 'paymentAmount'])->name('account.payment.report');
    Route::get('/account/paid/report', [AccountController::class, 'paidAmount'])->name('account.paid.report');
    Route::get('/account/payable/report', [AccountController::class, 'payableAmount'])->name('account.payable.report');
    Route::get('/account/expense/report', [AccountController::class, 'expenseReport'])->name('account.expense.report');

    // Setting Routes
    Route::get('/setting', [SettingController::class, 'setting'])->name('settings');
    Route::post('/setting/update', [SettingController::class, 'settingUpdate'])->name('settings.update');
    Route::get('/change/password', [SettingController::class, 'changePassword'])->name('change.password');
    Route::post('/change/password/update', [SettingController::class, 'changePasswordUpdate'])->name('change.password.update');
    Route::post('/setting/payment/account', [SettingController::class, 'account'])->name('setting.payment.account');
    Route::post('/setting/payment/account/cash', [SettingController::class, 'cashAccount'])->name('setting.payment.account.cash');
    Route::post('/account/balance/check', [SettingController::class, "checkBalance"])->name('account.blance.check');

    // AccountType Routes
    Route::get('/account-type', [AccountTypeController::class, 'index'])->name('account.type');
    Route::get('/account-type/list', [AccountTypeController::class, 'list'])->name('account.type.list');
    Route::post('/account-type/check', [AccountTypeController::class, 'accountTypCheck'])->name('account.type.check');
    Route::post('/account-type/store', [AccountTypeController::class, 'store'])->name('account.type.store');
    Route::post('/account-type/update', [AccountTypeController::class, 'update'])->name('account.type.update');
    Route::get('/account-type/{id}/deactive', [AccountTypeController::class, 'deactive'])->name('account.type.deactive');
    Route::get('/account-type/{id}/active', [AccountTypeController::class, 'active'])->name('account.type.active');
    Route::get('/account-type/{id}/edit', [AccountTypeController::class, 'edit'])->name('account.type.edit');
    // Route::get('/account-type/{id}/edit',[AccountTypeController::class,'edit'])->name("account.type.")

    // Asset Catetgory Routes
    Route::get('/asset-category', [AssetCategoryController::class, 'index'])->name('asset.category');
    Route::post('/asset-category/store', [AssetCategoryController::class, 'store'])->name('asset.category.store');
    Route::get('/asset-category/{id}/edit', [AssetCategoryController::class, 'edit'])->name('asset.category.edit');
    Route::post('/asset-category/update', [AssetCategoryController::class, 'update'])->name('asset.category.update');
    Route::get('/asset-category/{id}/delete', [AssetCategoryController::class, 'destroy'])->name('asset.category.destroy');

    // Asset Routes
    Route::get('/asset', [AssetController::class, 'index'])->name('asset');
    Route::get('/asset/all/json', [AssetController::class, 'allAssetJson'])->name('asset.all');
    Route::get('/asset/create', [AssetController::class, 'create'])->name('asset.create');
    Route::post('/asset/store', [AssetController::class, 'store'])->name('asset.store');
    Route::get('/asset/{id}/edit', [AssetController::class, 'edit'])->name('asset.edit');
    Route::post('/asset/update', [AssetController::class, 'update'])->name('asset.update');
    Route::get('/asset/{id}/delete', [AssetController::class, 'destroy'])->name('asset.destroy');
    Route::post('/asset/payment/account', [AssetController::class, 'account'])->name('asset.payment.account');

    // Stock Transfer
    Route::get('/stock-transfer', [StockTransferController::class, 'index'])->name('stock-transfer.index');
    Route::get('/stock-transfer/create', [StockTransferController::class, 'create'])->name('stock-transfer.create');
    Route::post('/stock-transfer/store', [StockTransferController::class, 'store'])->name('stock-transfer.store');
    Route::get('/stock-transfer/{id}/show', [StockTransferController::class, 'show'])->name('stock-transfer.show');
    Route::get('/stock-transfer/{stockTransfer}/edit', [StockTransferController::class, 'edit'])->name('stock-transfer.edit');
    Route::post('/stock-transfer/{id}/update', [StockTransferController::class, 'update'])->name('stock-transfer.update');
    Route::get('/stock-transfer/{id}/destroy', [StockTransferController::class, 'destroy'])->name('stock-transfer.destroy');


    /////// APP Management ///////
    // Customer
    Route::get("/app-customer/select2-ajax", [AppCustomerController::class, "select2Ajax"])->name("app.customer.select2-ajax");
    Route::get("/app-customer/export", [AppCustomerController::class, "export"])->name("app-customer.export");
    Route::post("/app-customer/export", [AppCustomerController::class, "export"])->name("app-customer.export.selected");
    Route::resource('app-customer', AppCustomerController::class);

    // Firm
    Route::resource('farms', FarmController::class);
    Route::get('farms/json/all', [FarmController::class, 'allAsJson'])->name('farms.json.all');
    Route::get('farms/json/findByCustomer/{id}', [FarmController::class, 'findByCustomerAsJson'])->name('farms.json.findByCustomer');
    Route::get('geocode/json/findDistrictsByDivision/{id}', [FarmController::class, 'findDistrictsByDivisionAsJson'])->name('districts.json.findByDivision');
    Route::get('geocode/json/findUpazilasByDivision/{id}', [FarmController::class, 'findUpazilasByDistrictAsJson'])->name('upazilas.json.findByDistrict');
    Route::get('geocode/json/findUnionsByUpazila/{id}', [FarmController::class, 'findUnionsByUpazilaAsJson'])->name('unions.json.findByUpazila');
    // Cattle Group
    Route::resource('cattle-groups', CattleGroupController::class);
    Route::get('cattle-groups/json/all', [CattleGroupController::class, 'allAsJson'])->name('cattle-groups.json.all');
    // Cattle Breed
    Route::resource('cattle-breeds', CattleBreedController::class);
    Route::get('cattle-breeds/json/all', [CattleBreedController::class, 'allAsJson'])->name('cattle-breeds.json.all');
    // Insurance Company
    Route::resource('insurance-companies', InsuranceCompanyController::class);
    Route::get('insurance-companies/json/all', [InsuranceCompanyController::class, 'allAsJson'])->name('insurance-companies.json.all');
    // Insurance Type
    Route::resource('insurance-types', InsuranceTypeController::class);
    Route::get('insurance-types/json/all', [InsuranceTypeController::class, 'allAsJson'])->name('insurance-types.json.all');
    // Cattle Disease
    Route::resource('cattle-diseases', CattleDiseaseController::class);
    Route::get('cattle-diseases/json/all', [CattleDiseaseController::class, 'allAsJson'])->name('cattle-diseases.json.all');
    // Cattle Vaccine
    Route::resource('cattle-vaccines', CattleVaccineController::class);
    Route::get('cattle-vaccines/json/all', [CattleVaccineController::class, 'allAsJson'])->name('cattle-vaccines.json.all');
    // Seed Company
    Route::resource('seed-companies', SeedCompanyController::class);
    Route::get('seed-companies/json/all', [SeedCompanyController::class, 'allAsJson'])->name('seed-companies.json.all');
    // Disease History
    Route::resource('cattle-disease-histories', DiseaseHistoryController::class);
    Route::get('cattle-disease-histories/json/all', [DiseaseHistoryController::class, 'allAsJson'])->name('cattle-disease-histories.json.all');
    // Disease History
    Route::resource('cattle-health-info', HealthInfoController::class);
    Route::get('cattle-health-info/json/all', [HealthInfoController::class, 'allAsJson'])->name('cattle-health-info.json.all');
    // Cattle
    Route::get('/cattle/json/all', [CattleController::class, 'allAsJson'])->name('cattle.json.all');
    Route::get('/cattle/json/findByFarm/{id}', [CattleController::class, 'findByFarmAsJson'])->name('cattle.json.findByFarm');
    Route::resource('cattle', CattleController::class);
    // Cattle Food
    Route::resource('cattle-foods', CattleFoodController::class);
    Route::get('cattle-foods/json/all', [CattleFoodController::class, 'allAsJson'])->name('cattle-foods.json.all');
    // Calf
    Route::resource('calves', CalfController::class);
    Route::get('calves/json/all', [CalfController::class, 'allAsJson'])->name('calves.json.all');
    // Calf Birth Problem
    Route::resource('calf-birth-problems', CalfBirthProblemController::class);
    Route::get('calf-birth-problems/json/all', [CalfBirthProblemController::class, 'allAsJson'])->name('calf-birth-problems.json.all');
    // Cattle Disease
    Route::resource('m-accounts', MAccountController::class);
    Route::get('balance', [MAccountController::class, 'balance'])->name('balance');
    Route::get('m-accounts/json/all', [MAccountController::class, 'allAsJson'])->name('m-accounts.json.all');

    // App Reports
    Route::get('app-report/milk-production', [AppDataReportController::class, 'milkProduction'])->name('app-report.milk-production');
    Route::get('app-report/vaccine', [AppDataReportController::class, 'vaccine'])->name('app-report.vaccine');
    Route::get('app-report/disease', [AppDataReportController::class, 'disease'])->name('app-report.disease');
    Route::get('app-report/impregnation', [AppDataReportController::class, 'impregnation'])->name('app-report.impregnation');
    Route::get('app-report/pregnancy', [AppDataReportController::class, 'pregnancy'])->name('app-report.pregnancy');
    Route::get('app-report/abortion', [AppDataReportController::class, 'abortion'])->name('app-report.abortion');
    Route::get('app-report/food-consumption', [AppDataReportController::class, 'foodConsumption'])->name('app-report.food-consumption');
    Route::get('app-report/weight-info', [AppDataReportController::class, 'weightInfo'])->name('app-report.weight-info');
    Route::get('app-report/farm', [AppDataReportController::class, 'farm'])->name('app-report.farm');

    // Admin Deposit
    Route::get('/admin-deposit/json', [AdminDepositController::class, 'json'])->name('admin-deposit.json.all');
    Route::get('/admin-deposit/{id}/delete', [AdminDepositController::class, 'destroy'])->name('admin-deposit.delete');
    Route::resource('/admin-deposit', AdminDepositController::class);

    // Admin to Agent Transaction
    Route::resource('/agent-points', AdminAgentTransactionController::class);

    // Agent to Customer Transaction
    Route::get('/customer-points/agent-points/{id}', [AgentCustomerTransactionController::class, 'totalAvailableAgentPoints'])->name('customer-points.agent-points');
    Route::get('/customer-points/total-loans/{id}', [AgentCustomerTransactionController::class, 'customerFinalLoanAmount'])->name('customer-points.total-loans');
    Route::get('/customer-points/available-points/{id}', [AgentCustomerTransactionController::class, 'customerAvailablePoints'])->name('customer-points.available-points');
    Route::get('/customer-points/json/all', [AgentCustomerTransactionController::class, 'allAsJson'])->name('customer-points.json.all');
    Route::resource('/customer-points', AgentCustomerTransactionController::class);

    // Agent Stock Transfer
    Route::resource('/agent-stock-transfer', AgentStockTransferController::class);

    // cow feed controller

    Route::resource('cow-feeds', CowFeedController::class);

    // Visit Info Routes
    Route::resource('visit-info', VisitInfoController::class);
    Route::get('visit-info/agent/report', [VisitInfoController::class, 'report'])->name('visit-info.report');
    Route::get('/customers/{customer}/invoices', [VisitInfoController::class, 'byCustomer'])
        ->name('customers.invoices');
    Route::get('visit-info/export/pdf', [VisitInfoController::class, 'exportPdf'])->name('visit-info.export.pdf');
    Route::get('visit-info/export/excel', [VisitInfoController::class, 'exportExcel'])->name('visit-info.export.excel');

    Route::controller(AdminAgentSaleListController::class)->group(function () {
        Route::get('agentsalelist', 'index')->name('agentsalelist.index');
        Route::get('agentsalelist/{user}', 'show')->name('agentsalelist.show');
    });

    Route::get('/stock-transfer/agent/create', [AgentToAgentTransferController::class, 'createAgentToAgent'])
        ->name('stock-transfer.agent.create');

    Route::post('/stock-transfer/agent', [AgentToAgentTransferController::class, 'storeAgentToAgent'])
        ->name('stock-transfer.agent.store');

    // Autocomplete/search restricted to the logged-in agent's available batches
    Route::get('/stock-transfer/agent/autocomplete', [AgentToAgentTransferController::class, 'agentTransferAutocomplete'])
        ->name('autocomplete.transfer.agent');

    // Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // show (and create) QR image for a user
    Route::get('/customer/qr/{id}', [QrController::class, 'showQr'])->name('customer.qr');
    Route::get('/user/qr/{id}', [QrController::class, 'showMyQr'])->name('qr.show');
    // scanner UI in Blade
    Route::get('/qr/scan', [QrController::class, 'scanPage'])->name('qr.scan');

    // Renewable Energy
    Route::get('renewable-energy/json/all', [RenewableEnergyController::class, 'allAsJson'])->name('renewable-energy.json.all');
    Route::get('renewable-energy/{id}/download-pdf', [RenewableEnergyController::class, 'downloadPdf'])->name('renewable-energy.download-pdf');
    Route::resource('renewable-energy', RenewableEnergyController::class);

    // scanner posts ciphertext (from QR) -> server decrypts -> responds with redirect URL
    Route::post('/scan/verify', [QrController::class, 'verify'])->name('qr.verify');

    // (optional) URL form if you ever put ciphertext inside a link and visit directly
    Route::get('/scan/t/{token}', [QrController::class, 'verifyLink'])->name('qr.verify.link');
});

Route::prefix('warehouse')
    ->name('warehouse.')
    ->group(function () {
        Route::get('/login', [WarehouseLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [WarehouseLoginController::class, 'login'])->name('login.store');
        Route::post('/logout', [WarehouseLoginController::class, 'logout'])->name('logout');

        Route::middleware(['auth:warehouse,warehouse_salesman'])->group(function () {
            Route::get('/dashboard', [WarehousePortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/stock', [WarehousePortalController::class, 'stock'])->name('stock');
            Route::get('/stock/received', [WarehousePortalController::class, 'receivedStock'])->name('stock.received');
            Route::get('/stock/received/{transfer}', [WarehousePortalController::class, 'receivedStockShow'])->name('stock.received.show');
            Route::get('/stock/lsp-received', [WarehousePortalController::class, 'salesmanReceivedStock'])->name('stock.lsp-received');
            Route::get('/stock/lsp-received/{assignment}', [WarehousePortalController::class, 'salesmanReceivedStockShow'])->name('stock.lsp-received.show');
            Route::get('/stock/products/{product}/lsp-distribution', [WarehousePortalController::class, 'productLspDistribution'])->name('stock.lsp-distribution');
            Route::get('/stock/print', [WarehousePortalController::class, 'stockPrint'])->name('stock.print');
            Route::get('/stock/pdf', [WarehousePortalController::class, 'stockPdf'])->name('stock.pdf');
            Route::get('/stock/excel', [WarehousePortalController::class, 'stockExcel'])->name('stock.excel');
            Route::get('/renewable-energy', [WarehousePortalController::class, 'renewableEnergyIndex'])->name('renewable-energy.index');
            Route::get('/renewable-energy/create', [WarehousePortalController::class, 'renewableEnergyCreate'])->name('renewable-energy.create');
            Route::post('/renewable-energy', [WarehousePortalController::class, 'renewableEnergyStore'])->name('renewable-energy.store');
            Route::get('/renewable-energy/{renewableEnergy}/edit', [WarehousePortalController::class, 'renewableEnergyEdit'])->name('renewable-energy.edit');
            Route::put('/renewable-energy/{renewableEnergy}', [WarehousePortalController::class, 'renewableEnergyUpdate'])->name('renewable-energy.update');
            Route::delete('/renewable-energy/{renewableEnergy}', [WarehousePortalController::class, 'renewableEnergyDestroy'])->name('renewable-energy.destroy');
            Route::get('/renewable-energy/geocode/districts/{division}', [WarehousePortalController::class, 'renewableEnergyDistricts'])->name('renewable-energy.districts');
            Route::get('/renewable-energy/geocode/upazilas/{district}', [WarehousePortalController::class, 'renewableEnergyUpazilas'])->name('renewable-energy.upazilas');
            Route::get('/renewable-energy/geocode/unions/{upazila}', [WarehousePortalController::class, 'renewableEnergyUnions'])->name('renewable-energy.unions');
            Route::get('/salesman-assignments', [WarehouseSalesmanAssignmentController::class, 'index'])->name('salesman-assignments.index');
            Route::get('/salesman-assignments/create', [WarehouseSalesmanAssignmentController::class, 'create'])->name('salesman-assignments.create');
            Route::post('/salesman-assignments', [WarehouseSalesmanAssignmentController::class, 'store'])->name('salesman-assignments.store');
            Route::get('/salesman-assignments/{warehouse_salesman_assignment}/print', [WarehouseSalesmanAssignmentController::class, 'print'])->name('salesman-assignments.print');
            Route::get('/salesman-assignments/{warehouse_salesman_assignment}', [WarehouseSalesmanAssignmentController::class, 'show'])->name('salesman-assignments.show');
            Route::get('/salesman-assignments/{warehouse_salesman_assignment}/edit', [WarehouseSalesmanAssignmentController::class, 'edit'])->name('salesman-assignments.edit');
            Route::match(['put', 'patch'], '/salesman-assignments/{warehouse_salesman_assignment}', [WarehouseSalesmanAssignmentController::class, 'update'])->name('salesman-assignments.update');
            Route::delete('/salesman-assignments/{warehouse_salesman_assignment}', [WarehouseSalesmanAssignmentController::class, 'destroy'])->name('salesman-assignments.destroy');
            Route::get('/salesman-returns', [WarehouseSalesmanReturnController::class, 'index'])->name('salesman-returns.index');
            Route::get('/salesman-returns/create', [WarehouseSalesmanReturnController::class, 'create'])->name('salesman-returns.create');
            Route::post('/salesman-returns', [WarehouseSalesmanReturnController::class, 'store'])->name('salesman-returns.store');
            Route::get('/salesman-returns/{warehouse_salesman_return}', [WarehouseSalesmanReturnController::class, 'show'])->name('salesman-returns.show');

            Route::get('/salesmen', [WarehousePortalReportController::class, 'salesmen'])->name('salesmen.index');
            Route::get('/salesmen/{warehouse_salesman}/stock', [WarehousePortalReportController::class, 'salesmanStock'])->name('salesmen.stock');
            Route::get('/salesmen/{warehouse_salesman}/sales-report', [WarehousePortalReportController::class, 'salesmanReport'])->name('salesmen.report');

            Route::get('/reports', [WarehousePortalReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/print', [WarehousePortalReportController::class, 'print'])->name('reports.print');
            Route::get('/reports/pdf', [WarehousePortalReportController::class, 'pdf'])->name('reports.pdf');
            Route::get('/reports/excel', [WarehousePortalReportController::class, 'excel'])->name('reports.excel');
            Route::get('/reports/area-manager-daily', [WarehousePortalReportController::class, 'areaManagerDaily'])->name('reports.area-manager-daily');
            Route::get('/reports/area-manager-daily/pdf', [WarehousePortalReportController::class, 'areaManagerDailyPdf'])->name('reports.area-manager-daily.pdf');
            Route::get('/reports/area-manager-daily/excel', [WarehousePortalReportController::class, 'areaManagerDailyExcel'])->name('reports.area-manager-daily.excel');

            Route::get('/wallet', [WarehousePortalController::class, 'wallet'])->name('wallet');
            Route::get('/farms', [WarehousePortalController::class, 'farms'])->name('farms');
            Route::post('/farms', [WarehousePortalController::class, 'storeFarm'])->name('farms.store');
            Route::put('/farms/{farm}', [WarehousePortalController::class, 'updateFarm'])->name('farms.update');
            Route::delete('/farms/{farm}', [WarehousePortalController::class, 'destroyFarm'])->name('farms.destroy');
            Route::get('/cattle', [WarehousePortalController::class, 'cattle'])->name('cattle');
            Route::get('/cattle/create', [WarehousePortalController::class, 'createCattle'])->name('cattle.create');
            Route::post('/cattle', [WarehousePortalController::class, 'storeCattle'])->name('cattle.store');
            Route::put('/cattle/{cattle}', [WarehousePortalController::class, 'updateCattle'])->name('cattle.update');
            Route::get('/calves', [WarehousePortalController::class, 'calves'])->name('calves');
            Route::get('/calves/create', [WarehousePortalController::class, 'createCalf'])->name('calves.create');
            Route::post('/calves', [WarehousePortalController::class, 'storeCalf'])->name('calves.store');
            Route::get('/due-amount', [WarehousePortalController::class, 'dueAmount'])->name('due-amount');
            Route::get('/daily-visits', [WarehousePortalController::class, 'dailyVisits'])->name('daily-visits');
            Route::get('/daily-visits/area-report', [WarehousePortalController::class, 'areaDailyVisitsReport'])->name('daily-visits.area-report');
            Route::get('/daily-visits/report', [WarehousePortalController::class, 'dailyVisitsReport'])->name('daily-visits.report');
            Route::get('/daily-visits/print', [WarehousePortalController::class, 'dailyVisitsPrint'])->name('daily-visits.print');
            Route::get('/daily-visits/create', [WarehousePortalController::class, 'manualVisitCreate'])->name('daily-visits.create');
            Route::post('/daily-visits', [WarehousePortalController::class, 'manualVisitStore'])->name('daily-visits.store');
            Route::get('/daily-visits/{visit}/edit', [WarehousePortalController::class, 'dailyVisitEdit'])->name('daily-visits.edit');
            Route::put('/daily-visits/{visit}', [WarehousePortalController::class, 'dailyVisitUpdate'])->name('daily-visits.update');
            Route::get('/daily-visits/{visit}', [WarehousePortalController::class, 'dailyVisitShow'])->name('daily-visits.show');
            Route::delete('/daily-visits/{visit}', [WarehousePortalController::class, 'dailyVisitDestroy'])->name('daily-visits.destroy');
            Route::get('/qr/scan', [QrController::class, 'warehouseScanPage'])->name('qr.scan');
            Route::post('/qr/verify', [QrController::class, 'verify'])->name('qr.verify');
            Route::get('/sales', [WarehousePortalController::class, 'index'])->name('sales.index');
            Route::get('/sales/create', [WarehousePortalController::class, 'create'])->name('sales.create');
            Route::post('/sales/customer', [WarehousePortalController::class, 'storeSalesBeneficiary'])->name('sales.beneficiary.store');
            Route::post('/sales', [WarehousePortalController::class, 'store'])->name('sales.store');
            Route::post('/sales/{warehouse_sale}/payments', [WarehousePortalController::class, 'payDue'])->name('sales.payments.store');
            Route::get('/sales/{warehouse_sale}/edit', [WarehousePortalController::class, 'editSale'])->name('sales.edit');
            Route::put('/sales/{warehouse_sale}', [WarehousePortalController::class, 'updateSale'])->name('sales.update');
            Route::get('/sales/{warehouse_sale}/invoice', [WarehousePortalController::class, 'invoice'])->name('sales.invoice');
            Route::get('/sales/{warehouse_sale}', [WarehousePortalController::class, 'show'])->name('sales.show');
        });

        Route::middleware('auth:warehouse,warehouse_salesman')->group(function () {
            Route::get('/beneficiaries', [WarehousePortalController::class, 'beneficiaries'])->name('beneficiaries.index');
            Route::get('/beneficiaries/create', [WarehousePortalController::class, 'createBeneficiary'])->name('beneficiaries.create');
            Route::get('/beneficiaries/import', [WarehousePortalController::class, 'importBeneficiariesForm'])->name('beneficiaries.import');
            Route::post('/beneficiaries/import', [WarehousePortalController::class, 'importBeneficiaries'])->name('beneficiaries.import.store');
            Route::get('/beneficiaries/import/template', [WarehousePortalController::class, 'beneficiaryImportTemplate'])->name('beneficiaries.import.template');
            Route::post('/beneficiaries', [WarehousePortalController::class, 'storeBeneficiary'])->name('beneficiaries.store');
            Route::get('/beneficiaries/{appCustomer}/edit', [WarehousePortalController::class, 'editBeneficiary'])->name('beneficiaries.edit');
            Route::put('/beneficiaries/{appCustomer}', [WarehousePortalController::class, 'updateBeneficiary'])->name('beneficiaries.update');
            Route::delete('/beneficiaries/{appCustomer}', [WarehousePortalController::class, 'destroyBeneficiary'])->name('beneficiaries.destroy');
        });
    });

//BioEnergyController

Route::get('/bioenery', [BioEnergyController::class, 'index'])->name('app-bioenergy.index');
Route::get('/create', [BioEnergyController::class, 'create'])->name('app-bioenergy.create');
Route::get('/store', [BioEnergyController::class, 'store'])->name('app-bioenergy.store');






Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return 'route cache complete';
});

Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return 'optimize complete';
});

Route::get('/optimize-clear', function () {
    $exitCode = Artisan::call('optimize:clear');
    return 'optimize complete';
});

Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return 'route clear complete';
});

Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return 'clear complete';
});

Route::get('/config-clear', function () {
    $exitCode = Artisan::call('config:clear');
    return 'clear complete';
});

Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return 'clear complete';
});

Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache cleared successfully!';
});
