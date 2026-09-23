@foreach ($contacts as $contact)
    <option value="{{ $contact->id }}">{{ $contact->name }} ({{ $contact->mobile }})</option>
@endforeach
