<?php

namespace App\Service;

use Illuminate\Http\Request;

abstract class PurchaseService
{
  public abstract function index(Request $request);
  public abstract function create();
  public abstract function store(Request $request);
  public abstract function show($id);
  public abstract function edit($id);
  public abstract function update(Request $request);
  public abstract function destroy($id);
  public abstract function invoice($id);
}
