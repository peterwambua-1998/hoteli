<?php

use App\Http\Controllers\AccountApprovalController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountUserController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BarStoreController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillCreditNoteController;
use App\Http\Controllers\BillDebitNoteController;
use App\Http\Controllers\BillReceiptController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CashierReports;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentItemController;
use App\Http\Controllers\FrontOfficeStoreController;
use App\Http\Controllers\GoodReceiveNoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KitchenStoreController;
use App\Http\Controllers\LpoController;
use App\Http\Controllers\MainStoreController;
use App\Http\Controllers\MaintenanceStoreController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\MLoginController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PosBarController;
use App\Http\Controllers\PosKitchenController;
use App\Http\Controllers\PosPoolController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\RefundRequestController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\WithholdingTaxController;
use App\Models\FrontOfficeStore;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resource('users', UserController::class);

    // department and department items
    Route::resource('departments', DepartmentController::class);

    // material requisition
    Route::get('/bar-requisition', [DepartmentController::class, 'barRequisitionView'])->name('bar.requisition.view');
    Route::get('/kitchen-requisition', [DepartmentController::class, 'kitchenRequisitionView'])->name('kitchen.requisition.view');
    Route::get('/house-requisition', [DepartmentController::class, 'houseRequisitionView'])->name('house.requisition.view');
    Route::get('/office-requisition', [DepartmentController::class, 'officeRequisitionView'])->name('office.requisition.view');
    Route::get('/maintenance-requisition', [DepartmentController::class, 'maintenanceRequisitionView'])->name('maintenance.requisition.view');

    Route::get('/bar-requisition/create', [DepartmentController::class, 'barRequisitionCreate'])->name('bar.requisition.create');
    Route::get('/kitchen-requisition/create', [DepartmentController::class, 'kitchenRequisitionCreate'])->name('kitchen.requisition.create');
    Route::get('/house-requisition/create', [DepartmentController::class, 'houseRequisitionCreate'])->name('house.requisition.create');
    Route::get('/office-requisition/create', [DepartmentController::class, 'officeRequisitionCreate'])->name('office.requisition.create');
    Route::get('/maintenance-requisition/create', [DepartmentController::class, 'maintenanceRequisitionCreate'])->name('maintenance.requisition.create');

    Route::post('/requisition/store', [DepartmentController::class, 'requisitionStore'])->name('requisition.store');
    
    // department items
    Route::resource('department-items', DepartmentItemController::class);

    // accounts and account-users
    Route::resource('accounts', AccountController::class);
    Route::get('/running-balance/{id}', [AccountController::class, 'runningBalance']);
    Route::get('/statement/account/{id}', [AccountController::class, 'statementOfAccount'])->name('accounts.statement');
    Route::resource('account-users', AccountUserController::class);
    Route::post('/query-accounts', [AccountController::class, 'query'])->name('accounts.query');

    // rooms
    Route::resource('rooms', RoomController::class);
    Route::resource('room-types', RoomTypeController::class);
    Route::post('/search-rooms', [RoomController::class, 'searchRoom'])->name('search-rooms');

    // reservations
    Route::resource('reservations', BookingController::class);
    Route::get('/reservation/add/{account_id}', [BookingController::class, 'createReservation'])->name('reservations.add');
    Route::post('/reservation/activate', [BookingController::class, 'activateReservation'])->name('activate.reservation');
    Route::get('/reservation/checkout/{id}/{account_id}', [CheckoutController::class, 'checkoutPage'])->name('booking.checkout');
    Route::get('/reservation/invoice/print/{id}', [CheckoutController::class, 'print'])->name('reservation.invoice.print');
    Route::post('/reservation/invoice/pay', [CheckoutController::class, 'payOrder'])->name('reservation.invoice.pay');
    Route::post('/checkout-under-company', [CheckoutController::class, 'checkoutUnderCompany'])->name('checkout.under.company');
    Route::post('/reservation/discount', [CheckoutController::class, 'addDiscountCheckout'])->name('reservation.discount');

    // check in express guest
    Route::get('/checkin', [BookingsController::class, 'createBooking'])->name('checkin.create');
    Route::post('/checkin/save/express', [BookingsController::class, 'expressCheckin'])->name('checkin.express.save');

    // check in express company
    Route::get('/checkin/company', [BookingsController::class, 'createBookingCompany'])->name('checkin.create.company');
    Route::post('/checkin/company/save/express', [BookingsController::class, 'expressCheckinCompany'])->name('checkin.company.express.save');
    
    // proforma invoice
    // Route::get('/proforma', [ProformaInvoiceController::class, 'index'])->name('proforma.index');
    Route::get('/proforma/create/{account_id}', [ProformaInvoiceController::class, 'create'])->name('proforma.create');
    Route::get('/proforma/show/{id}', [ProformaInvoiceController::class, 'show'])->name('proforma.show');
    Route::get('/proforma/edit/{id}', [ProformaInvoiceController::class, 'edit'])->name('proforma.edit');
    Route::post('/proforma/save', [ProformaInvoiceController::class, 'store'])->name('proforma.store');
    Route::patch('/proforma/update{id}', [ProformaInvoiceController::class, 'update'])->name('proforma.update');
    Route::post('/proforma/convert', [ProformaInvoiceController::class, 'convertToInvoice'])->name('proforma.convert');

    // invoice
    Route::get('/invoice/create/{account_id}', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::get('/invoice/show/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::post('/invoice/save', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/edit/{id}',[InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::patch('/invoice/update/{id}',[InvoiceController::class, 'update'])->name('invoice.update');
    Route::post('/invoice/credit-note/crate',[InvoiceController::class, 'creditNote'])->name('invoice.creditNote');

    // quotation
    Route::get('/quotation/create/{account_id}', [QuotationController::class, 'create'])->name('quotation.create');
    Route::get('/quotation/show/{id}',[QuotationController::class, 'show'])->name('quotation.show');
    Route::post('/quotation/save', [QuotationController::class, 'store'])->name('quotation.store');
    Route::post('/quotation/convert', [QuotationController::class, 'convertToInvoice'])->name('quotation.convert');
    Route::post('/quotation/download/pdf', [QuotationController::class, 'downloadPdf'])->name('quotation.download.pdf');

    // credit note
    Route::get('/credit-note/create/{invoice_item_id}', [CreditNoteController::class, 'create'])->name('credit-note.create');
    Route::post('/credit-note/save', [CreditNoteController::class, 'store'])->name('credit-note.store');
    Route::get('/credit-note/show/{id}', [CreditNoteController::class, 'show'])->name('credit-note.show');

    // debit note
    Route::get('/debit-note/create/{invoice_item_id}', [DebitNoteController::class, 'create'])->name('debit-note.create');
    Route::post('/debit-note/save', [DebitNoteController::class, 'store'])->name('debit-note.store');
    Route::get('/debit-note/show/{id}', [DebitNoteController::class, 'show'])->name('debit-note.show');

    // withholding tax
    Route::get('/withholding/create/{invoice_id}', [WithholdingTaxController::class, 'create'])->name('withholding.create');
    Route::post('/withholding/save', [WithholdingTaxController::class, 'store'])->name('withholding.store');

    // lpo
    Route::get('/lpo/download/{id}', [LpoController::class, 'downloadFile'])->name('lpo.download');
    Route::post('/lpo/save', [LpoController::class, 'store'])->name('lpo.store');

    // receipt
    Route::get('/receipt/show/{id}', [ReceiptController::class,'show'])->name('receipt.show');
    Route::post('/receipt/store', [ReceiptController::class,'receiptGenerator'])->name('receipt.store');
    Route::get('/receipt/print/{id}', [ReceiptController::class, 'print'])->name('receipt.print');
    Route::post('/receipt/withholding', [ReceiptController::class, 'withHolding'])->name('receipt.withHolding');
    
    // bank accounts 
    Route::resource('bank-account',BankAccountController::class);
    Route::get('/bank-accounts-analytics',[BankAccountController::class, 'analytics']);
    Route::get('/bank-account/statement/show/{id}', [BankAccountController::class, 'bankStatementView'])->name('bank.statement.view');
    Route::get('/bank-account/statement/{id}', [BankAccountController::class, 'bankStatement'])->name('bank.statement.data');

    // refund request
    Route::get('/refund-request',[RefundRequestController::class, 'index'])->name('refund-request.index');
    Route::post('/refund-request/store',[RefundRequestController::class, 'store'])->name('refund-request.store');
    Route::post('/refund-request/approve',[RefundRequestController::class, 'approve'])->name('refund-request.approve');

    // refund
    Route::post('refund/store', [RefundController::class, 'store'])->name('refund.store');

    // product category
    Route::get('/category',[CategoryController::class,'index'])->name('category.index');
    Route::post('/category/save',[CategoryController::class,'store'])->name('category.store');
    Route::patch('/category/update/{id}',[CategoryController::class,'update'])->name('category.update');

    // products
    Route::resource('products', ProductController::class)->except(['create','show','edit']);
    Route::post('/products/query',[ProductController::class,'queryItems'])->name('query.items');

    //suppliers
    Route::resource('suppliers', SupplierController::class);

    // purchase order
    Route::get('/purchase-order', [PurchaseOrderController::class, 'index'])->name('purchase.order.index');
    Route::get('/purchase-order/create', [PurchaseOrderController::class, 'create'])->name('purchase.order.create');
    Route::get('/purchase-order/show/{id}', [PurchaseOrderController::class, 'show'])->name('purchase.order.show');
    Route::post('/purchase-order/store', [PurchaseOrderController::class, 'store'])->name('purchase.order.store');
    Route::patch('/purchase-order/update', [PurchaseOrderController::class, 'update'])->name('purchase.order.update');
    Route::post('/purchase-order/reject', [PurchaseOrderController::class, 'reject'])->name('purchase.order.reject');

    // goods received note
    Route::get('/goods-receive', [GoodReceiveNoteController::class, 'index'])->name('goods.receive.index');
    Route::get('/goods-receive/create/{purchase_order_id}', [GoodReceiveNoteController::class, 'create'])->name('goods.receive.create');
    Route::get('/goods-receive/show/{id}', [GoodReceiveNoteController::class, 'show'])->name('goods.receive.show');
    Route::post('/goods-receive/store', [GoodReceiveNoteController::class, 'store'])->name('goods.receive.store');
    Route::get('/goods-receive/generate', [GoodReceiveNoteController::class, 'grnWithoutPOView'])->name('goods.receive.generate');
    Route::post('/goods-receive/generate/store', [GoodReceiveNoteController::class, 'grnWithoutPOStore'])->name('goods.receive.generate.store');
    // bill
    Route::get('/bill', [BillController::class, 'index'])->name('bill.index');
    Route::get('/bill/create', [BillController::class, 'create'])->name('bill.create');
    Route::post('/bill/store', [BillController::class, 'store'])->name('bill.store');
    Route::get('/bill/show/{id}', [BillController::class, 'show'])->name('bill.show');

    // bill credit note
    Route::get('/bill-credit-note/create/{invoice_item_id}', [BillCreditNoteController::class, 'create'])->name('bill.credit-note.create');
    Route::post('/bill-credit-note/save', [BillCreditNoteController::class, 'store'])->name('bill.credit-note.store');
    Route::get('/bill-credit-note/show/{id}', [BillCreditNoteController::class, 'show'])->name('bill.credit-note.show');

    // bill debit note
    Route::get('/bill-debit-note/create/{invoice_item_id}', [BillDebitNoteController::class, 'create'])->name('bill.debit-note.create');
    Route::post('/bill-debit-note/save', [BillDebitNoteController::class, 'store'])->name('bill.debit-note.store');
    Route::get('/bill-debit-note/show/{id}', [BillDebitNoteController::class, 'show'])->name('bill.debit-note.show');

    // bill receipt
    Route::get('/bill/receipt/show/{id}', [BillReceiptController::class,'show'])->name('bill.receipt.show');
    Route::post('/bill/receipt/store', [BillReceiptController::class,'generateReceipt'])->name('bill.receipt.store');

    // main store goods
    Route::get('/main-store', [MainStoreController::class, 'index'])->name('main.store.index');
    Route::get('/main-store/requisitions', [MainStoreController::class, 'requisitionPage'])->name('main.store.requisition');
    Route::get('/main-store/requisitions/issue/{id}',[MainStoreController::class, 'requisitionCreate'])->name('requisition.issue.page');
    Route::post('/main-store/requisitions/issue',[MainStoreController::class, 'requisitionIssue'])->name('requisition.issue.store');
    
    // main store adjust
    Route::get('/main-store/adjust', [MainStoreController::class,  'adjustStock'])->name('main.store.adjust');
    Route::post('/main-store/adjust/store', [MainStoreController::class,  'adjustStockStore'])->name('main.store.adjust.store');
    
    // kitchen store, pos, recipe
    Route::get('/kitchen-store', [KitchenStoreController::class, 'index'])->name('kitchen.store.index');
    
    Route::get('/kitchen-store/recipe', [KitchenStoreController::class,'createMenuItem'])->name('kitchen.store.recipe');
    Route::post('/kitchen-store/recipe/store', [KitchenStoreController::class,'storeMenuItem'])->name('kitchen.store.recipe.store');
    Route::post('/kitchen-store/search', [KitchenStoreController::class,'queryItems'])->name('kitchen.search');

    Route::get('/kitchen-store/pos', [PosKitchenController::class, 'posPage'])->name('kitchen.store.pos');
    Route::post('/kitchen-store/search-item-orders', [PosKitchenController::class, 'searchItem'])->name('kitchen.search-item-orders');
    Route::post('/kitchen-store/orders/save', [PosKitchenController::class, 'storeOrder'])->name('kitchen.order.store');
    Route::get('/kitchen-store/orders', [PosKitchenController::class, 'orders'])->name('kitchen.orders');
    Route::get('/kitchen-store/orders/{id}/print', [PosKitchenController::class, 'ordersPrint'])->name('kitchen.orders.print');
    Route::post('/kitchen-store/orders/receipt', [PosKitchenController::class, 'ordersPayment'])->name('kitchen.order.payment');

    Route::get('/kitchen-store/pos/add/order/{id}', [PosKitchenController::class, 'addToExistingOrderPage'])->name('kitchen.add_existing.page');
    Route::post('/kitchen-store/pos/add/order/save', [PosKitchenController::class, 'addToExistingOrderSave'])->name('kitchen.add_existing.save');

    // kitchen store adjust
    Route::get('/kitchen-store/adjust', [KitchenStoreController::class,  'adjustStock'])->name('kitchen.store.adjust');
    Route::post('/kitchen-store/adjust/store', [KitchenStoreController::class,  'adjustStockStore'])->name('kitchen.store.adjust.store');

    // bar store
    Route::get('/bar-store', [BarStoreController::class, 'index'])->name('bar.store.index');
    Route::get('/bar-store/pos', [PosBarController::class, 'posPage'])->name('bar.store.pos');
    

    Route::get('/bar-store/recipe', [BarStoreController::class,'createMenuItem'])->name('bar.store.recipe');
    Route::post('/bar-store/recipe/store', [BarStoreController::class,'storeMenuItem'])->name('bar.store.recipe.store');
    Route::post('/bar-store/search', [BarStoreController::class,'queryItems'])->name('bar.search');

    Route::get('/bar-store/orders', [PosBarController::class, 'orders'])->name('bar.orders');
    Route::get('/bar-store/orders/{id}/print', [PosBarController::class, 'ordersPrint'])->name('bar.orders.print');
    Route::post('/bar-store/orders/receipt', [PosBarController::class, 'ordersPayment'])->name('bar.order.payment');
    Route::post('/bar-store/orders/save', [PosBarController::class, 'storeOrder'])->name('bar.order.store');
    Route::post('/search-item-orders', [PosBarController::class, 'searchItem'])->name('search-item-orders');

    Route::get('/bar-store/pos/add/order/{id}', [PosBarController::class, 'addToExistingOrderPage'])->name('bar.add_existing.page');
    Route::post('/bar-store/pos/add/order/save', [PosBarController::class, 'addToExistingOrderSave'])->name('bar.add_existing.save');

    // main store adjust
    Route::get('/bar-store/adjust', [BarStoreController::class,  'adjustStock'])->name('bar.store.adjust');
    Route::post('/bar-store/adjust/store', [BarStoreController::class,  'adjustStockStore'])->name('bar.store.adjust.store');
    
    // maintenance store
    Route::get('/maintenance-store', [MaintenanceStoreController::class, 'index'])->name('maintenance.store.index');

    // stock adjustment
    Route::post('/stock-adjustment',[StockAdjustmentController::class, 'store'])->name('adjustment.store');

    // meal plan
    Route::resource('meal-plan', MealPlanController::class);

    // cashier routes
    Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders.index');
    Route::post('/order/pay', [CashierController::class, 'payOrder'])->name('cashier.orders.pay');
    Route::post('/order/void', [CashierController::class, 'voidOrder'])->name('cashier.orders.void');
    Route::get('/cash-orders', [CashierController::class, 'systemCash'])->name('cash.orders');
    Route::get('/cashier-reports', [CashierReports::class, 'accountSales'])->name('cashier.reports');

    // reservation
    Route::get('/booking', [ReservationController::class, 'index'])->name('b.index');
    Route::post('/booking/create', [ReservationController::class, 'create'])->name('b.create');
    Route::post('/booking/store', [ReservationController::class, 'store'])->name('b.store');
    Route::post('/booking/adjust', [ReservationController::class, 'adjustBooking'])->name('b.adjust');
    Route::post('/booking/approve', [ReservationController::class, 'approveAmendment'])->name('b.approve');

    // packages
    Route::resource('packages', PackageController::class);

    // store account for approval
    Route::get('/account/approval', [AccountApprovalController::class, 'index'])->name('account.approval.index');
    Route::post('/account-approval/store', [AccountApprovalController::class, 'store'])->name('account.approval.store');
    Route::post('/account-approval/update', [AccountApprovalController::class, 'update'])->name('account.approval.update');

    // system days
    Route::get('/system/day', [DayController::class, 'index'])->name('system.days.index');
    Route::post('/system/day/store', [DayController::class, 'store'])->name('system.days.store');
    Route::post('/system/day/end', [DayController::class, 'endDay'])->name('system.days.end');
    Route::get('/collected/cash', [DayController::class,'systemCashCollected'])->name('collected.cash');

    // imports category
    Route::get('/import/category', [ImportController::class, 'categoryPage'])->name('import.category.page');
    Route::post('/import/category/save', [ImportController::class, 'categorySave'])->name('import.category.save');

    Route::get('/import/product', [ImportController::class, 'productPage'])->name('import.product.page');
    Route::post('/import/product/save', [ImportController::class, 'productSave'])->name('import.product.save');

    Route::get('/transfer', [ImportController::class, 'transferToMainStor']);


    // get system collected cash

    // change pos
    Route::get('/select/point-of-sale', [WaiterController::class, 'selectPos'])->name('select.pos');

    // swimming pos
    Route::get('/pool/pos', [PosPoolController::class, 'posPage'])->name('pool.pos.page');
    Route::post('/pool/pos/save', [PosPoolController::class, 'storeOrder'])->name('pool.pos.save');
    Route::post('/swimming/search-item-orders', [PosPoolController::class, 'searchItem'])->name('pool.search-item-orders');

    Route::get('/pool-store/pos/add/order/{id}', [PosPoolController::class, 'addToExistingOrderPage'])->name('pool.add_existing.page');
    Route::post('/pool-store/pos/add/order/save', [PosPoolController::class, 'addToExistingOrderSave'])->name('pool.add_existing.save');

    // waiter routes
    Route::get('/waiter/orders', [WaiterController::class, 'orders'])->name('waiter.orders');
    Route::get('/waiter/orders/add/order/{id}', [WaiterController::class, 'addToOrderSelectPos'])->name('waiter.orders.add.select.pos');

    // test cash
    Route::get('/test/cash', [CashierController::class, 'systemCash']);


    // font office direct sales and store
    Route::get('/front-office/store', [FrontOfficeStoreController::class, 'index'])->name('front.office.index');
    Route::get('/front-office/adjust', [FrontOfficeStoreController::class,  'adjustStock'])->name('front.store.adjust');
    Route::post('/front-office/adjust/store', [FrontOfficeStoreController::class,  'adjustStockStore'])->name('front.store.adjust.store');
    
    Route::get('/front-office/pos', [FrontOfficeStoreController::class, 'posPage'])->name('front.office.pos');
    Route::post('/front-office/search', [FrontOfficeStoreController::class, 'searchItem'])->name('front.office.search');
    Route::get('/front-office/order/list', [FrontOfficeStoreController::class, 'orders'])->name('front.office.orderList');
    Route::post('/front-office/order/store', [FrontOfficeStoreController::class, 'storeOrder'])->name('front.office.storeOrder');
    Route::post('/front-office/order/pay', [FrontOfficeStoreController::class, 'payOrder'])->name('front.office.payOrder');
    Route::post('/front-office/order/void', [FrontOfficeStoreController::class, 'voidOrder'])->name('front.office.void');


    // reports
    Route::get('/dept-sales', [HomeController::class, 'invoicePosting'])->name('dept.sales');
    Route::get('/account-cash-flows', [HomeController::class, 'cashInflows'])->name('cashflows');

    // transfer to main store
    Route::get('/main-transfer', [MainStoreController::class, 'transfer']);
    Route::get('/report-saves', [HomeController::class, 'salesPerDay']);
    Route::post('/account-cash-flows/query', [HomeController::class, 'salesPerDayQuery'])->name('cashflows.query');

});

Route::post('/my-login', [MLoginController::class, 'authenticate'])->name('my-login');

