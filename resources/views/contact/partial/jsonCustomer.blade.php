@foreach ($customers as $customer)
    <option value="{{ $customer->id }}"> {{ $customer->name }} ({{ $customer->mobile }})</option>
@endforeach
