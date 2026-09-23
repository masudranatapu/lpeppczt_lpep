@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <div class="col-4">
            <div class="card-box table-responsive mt-4">
                <div class="row">
                    <div class="col-md-12">
                        @isset($type)
                            <h4 class="page-title">Update Asset Category</h4>
                            <form action="{{ route('asset.category.update') }}" method="POST" class="mt-5">
                                @csrf
                                <input type="hidden" name="id" value="{{ $type->id }}">
                                <div class="form-group">
                                    <label for="class_name"> Asset Category </label>
                                    <input type="text" name="asset_category" value="{{ $type->asset_category }}"
                                        id="class_name" class="form-control">
                                    @error('asset_category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="pull-right mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        @else
                            <h5 class="page-title">{{ __('page.assetcategory')[0] }}</h5>
                            <form action="{{ route('asset.category.store') }}" method="POST" class="mt-1">
                                @csrf
                                <div class="form-group">
                                    <label for="class_name"> {{ __('page.assetcategory')[1] }} </label>
                                    <input type="text" name="asset_category" id="class_name" class="form-control">
                                </div>

                                <div class="pull-right mt-3">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        @endisset
                    </div>


                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card-box table-responsive mt-4">
                <div class="row">

                    <div class="col-md-12">
                        <h4 class="m-t-0 header-title mb-4"><b>{{ __('page.assetcategory')[3] }}</b></h4>
                        <table id="datatable" class="table table-bordered mt-5">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>{{ __('page.assetcategory')[4] }}</th>
                                    <th>{{ __('page.assetcategory')[5] }}</th>
                                    <th>{{ __('page.assetcategory')[6] }}</th>
                                    <th>{{ __('page.assetcategory')[7] }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $business = currentBranch();
                                @endphp
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->asset_category }}</td>
                                        <td>{{ $business->name }}</td>
                                        <td>
                                            <div class="btn-group btn-sm">
                                                <a href="{{ route('asset.category.edit', $item->id) }}"
                                                    style="cursor: pointer" id="edit-district"
                                                    class="btn btn-info btn-sm"><i class="dripicons-document-edit"></i></a>
                                                <a href="{{ route('asset.category.destroy', $item->id) }}"
                                                    class="btn btn-danger btn-sm" id="delete"><i
                                                        class="dripicons-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- end row -->


@endsection
