@php
    $rows = $assignments instanceof \Illuminate\Pagination\AbstractPaginator ? $assignments->getCollection() : $assignments;
    $startNumber = $assignments instanceof \Illuminate\Pagination\AbstractPaginator ? $assignments->firstItem() : 1;
@endphp
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>SL</th>
                <th>Date</th>
                <th>Invoice</th>
                <th>Area Office</th>
                <th>LSP</th>
                <th>Products</th>
                <th class="text-right">Quantity</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $assignment)
                <tr>
                    <td>{{ $startNumber + $loop->index }}</td>
                    <td>{{ $assignment->assignment_date }}</td>
                    <td>{{ $assignment->invoice_no }}</td>
                    <td>{{ $assignment->warehouse?->name }}</td>
                    <td>{{ $assignment->salesman?->name }}</td>
                    <td>{{ $assignment->items->count() }}</td>
                    <td class="text-right">{{ number_format($assignment->total_quantity, 2) }}</td>
                    <td class="text-nowrap">
                        <a class="btn btn-info btn-sm" href="{{ route($showRoute, $assignment) }}">View</a>
                        @if(!empty($editRoute))
                            <a class="btn btn-warning btn-sm" href="{{ route($editRoute, $assignment) }}">Edit</a>
                        @endif
                        @if(!empty($deleteRoute))
                            <form class="d-inline delete-form" method="POST" action="{{ route($deleteRoute, $assignment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No assignments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($assignments instanceof \Illuminate\Pagination\AbstractPaginator)
    {{ $assignments->links() }}
@endif
