@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive mt-4" style="border-top: 3px solid #2aa9b9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-t-0 header-title mb-5"><b>Role Create</b></h4>
                    </div>
                </div>
                <form action="{{ route('role.store') }}" method="POST">
                    @csrf

                    <div class="mt-3">
                        <div class="form-group">
                            <label for="">Role Name</label>
                            <input type="text" name="role_name" class="form-control" placeholder="Role Name" required>
                        </div>
                        <br>
                        <div class="mb-3">
                            <label for=""><b>Permission</b></label>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Supplier</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" id="supplier" name="permissions[]">
                                            <input id="checkboxsss1" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="s1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsss1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxssss1" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="s2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxssss1"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsssss1" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="s3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsssss1"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxssssss1" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="s4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxssssss1"> Delete </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="supplier_pay" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="supplier_pay" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="supplier_pay"> Pay </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Customer</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxv2" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="c1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxv2"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkbox3" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="c2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkbox3"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkbox4" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="c3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkbox4"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkbox5" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="c4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkbox5"> Delete </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="customer_pay" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="customer_pay" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="customer_pay"> Pay </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkbox6" onclick="setValue('supplier','sup')" type="checkbox"
                                                name="permissions[]" value="cg1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkbox6"> Customer Group </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Product</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" id="product" name="permissions[]">
                                            <input id="checkboxp1" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="p1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxp1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxp2" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="p2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxp2"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxp3" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="p3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxp3"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxp4" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="p4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxp4"> Delete </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxac" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="pad1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxac"> Active/Deactive </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxps1" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="ps1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxps1"> Stock Manage </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Category</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="category1" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="cat1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="category1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="category12" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="cat2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="category12"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="category13" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="cat3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="category13"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="category14" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="cat4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="category14"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Brand</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="brand1" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="bra1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="brand1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="brand12" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="bra2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="brand12"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="brand13" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="bra3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="brand13"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="brand14" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="bra4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="brand14"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Unit</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="unit1" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="uni1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="unit1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="unit12" onclick="setValue('product','pro')" type="checkbox"
                                                name="permissions[]" value="uni2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="unit12"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="unit13" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="uni3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="unit13"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="unit14" type="checkbox" onclick="setValue('product','pro')"
                                                name="permissions[]" value="uni4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="unit14"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Purchase</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" id="purchase" name="permissions[]">
                                            <input id="checkboxpu1" onclick="setValue('purchase','pur')" type="checkbox"
                                                name="permissions[]" value="pu1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxpu1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxpu1211" onclick="setValue('purchase','pur')" type="checkbox"
                                                name="permissions[]" value="pu2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxpu1211"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxpu11" onclick="setValue('purchase','pur')" type="checkbox"
                                                name="permissions[]" value="pu3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxpu11"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxpu111" onclick="setValue('purchase','pur')" type="checkbox"
                                                name="permissions[]" value="pu4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxpu111"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Stock</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="stock">
                                            <input id="checkboxst1" onclick="setValue('stock','stock')" type="checkbox"
                                                name="permissions[]" value="st1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxst1"> Stock Manage </label>
                                        </div>
                                    </label>

                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Sale</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="sale">
                                            <input id="checkboxsa1" onclick="setValue('sale','sale')" type="checkbox"
                                                name="permissions[]" value="sa1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsa1"> Pos </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsaa1" onclick="setValue('sale','sale')" type="checkbox"
                                                name="permissions[]" value="sa2" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsaa1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsssdf1" onclick="setValue('sale','sale')" type="checkbox"
                                                name="permissions[]" value="sa3" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsssdf1"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsal1" onclick="setValue('sale','sale')" type="checkbox"
                                                name="permissions[]" value="sa4" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsal1"> Delete </label>
                                        </div>
                                    </label>

                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsr1" onclick="setValue('sale','sale')" type="checkbox"
                                                name="permissions[]" value="sr1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxsr1"> Sale Return </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Quotation</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="quotation">
                                            <input id="checkboxq1" type="checkbox"
                                                onclick="setValue('quotation','quotation')" name="permissions[]" value="q1"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxq1"> Add Quotation </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxqu1" type="checkbox"
                                                onclick="setValue('quotation','quotation')" name="permissions[]" value="q2"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxqu1"> View </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxaaaaa1" type="checkbox"
                                                onclick="setValue('quotation','quotation')" name="permissions[]" value="q3"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxaaaaa1"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Report</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" id="report" name="permissions[]">
                                            <input id="checkboxre1" type="checkbox" onclick="setValue('report','report')"
                                                name="permissions[]" value="re1" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxre1"> Report Manage </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Expense</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="expense">
                                            <input id="checkboxex1" onclick="setValue('expense','expense')"
                                                name="permissions[]" value="ex1" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxex1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxexx1" onclick="setValue('expense','expense')"
                                                name="permissions[]" value="ex2" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxexx1"> Add </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxex31" onclick="setValue('expense','expense')"
                                                name="permissions[]" value="ex3" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxex31"> Delete </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxex4" onclick="setValue('expense','expense')"
                                                name="permissions[]" value="ex4" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxex4"> Expense Category </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Accounts</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="accounts">
                                            <input id="checkboxac1" name="permissions[]"
                                                onclick="setValue('accounts','accounts')" value="ac1" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxac1"> Receive Report </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxac21" name="permissions[]"
                                                onclick="setValue('accounts','accounts')" value="ac2" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxac21"> Payment Report </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxacc1" type="checkbox"
                                                onclick="setValue('accounts','accounts')" name="permissions[]" value="ac3"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxacc1"> Expense Report </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Employee</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="employee">
                                            <input id="checkboxem1" name="permissions[]"
                                                onclick="setValue('employee','employee')" value="em1" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxem1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxemm1" name="permissions[]"
                                                onclick="setValue('employee','employee')" value="em2" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxemm1"> Create </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxem111" name="permissions[]"
                                                onclick="setValue('employee','employee')" value="em3" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxem111"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxemdf1" name="permissions[]"
                                                onclick="setValue('employee','employee')" value="em4" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxemdf1"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Salary</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsallll1" onclick="setValue('employee','employee')"
                                                name="permissions[]" value="sal1" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxsallll1"> Pay Salary </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxsall1" onclick="setValue('employee','employee')"
                                                name="permissions[]" value="sal2" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxsall1"> Paid Salary List </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">User</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="user">
                                            <input id="checkboxu1" name="permissions[]" onclick="setValue('user','user')"
                                                value="u1" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxu1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxu11" name="permissions[]" onclick="setValue('user','user')"
                                                value="u2" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxu11"> Create </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxu3" name="permissions[]" onclick="setValue('user','user')"
                                                value="u3" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxu3"> Edit </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxu4" name="permissions[]" onclick="setValue('user','user')"
                                                value="u4" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxu4"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxro1" name="permissions[]" onclick="setValue('user','user')"
                                                value="ro1" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxro1"> List </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxroo1" name="permissions[]" onclick="setValue('user','user')"
                                                value="ro2" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxroo1"> Create </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxro3" name="permissions[]" onclick="setValue('user','user')"
                                                value="ro3" type="checkbox" data-parsley-multiple="groups"
                                                data-parsley-mincheck="2" data-parsley-id="69">
                                            <label for="checkboxro3"> Delete </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Setting</label>
                            <div class="col-sm-9">
                                <div class="mt-2">
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input type="hidden" name="permissions[]" id="setting">
                                            <input id="checkboxsttt1" name="permissions[]"
                                                onclick="setValue('setting','setting')" value="st1" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxsttt1"> Business Setting </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxstt1" name="permissions[]"
                                                onclick="setvalue('setting','setting')" value="st2" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxstt1"> Business Branches </label>
                                        </div>
                                    </label>
                                    <label class="custom-control custom-checkbox">
                                        <div class="checkbox checkbox-pink">
                                            <input id="checkboxstttddfafd1" name="permissions[]"
                                                onclick="setvalue('setting','setting')" value="st3" type="checkbox"
                                                data-parsley-multiple="groups" data-parsley-mincheck="2"
                                                data-parsley-id="69">
                                            <label for="checkboxstttddfafd1"> VAT/Sd Group </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>


                        <div class="text-right mt-5">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>




                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <script>
        function setValue(id, val) {
            $(`#${id}`).val(val)
        }
    </script>

@endsection
