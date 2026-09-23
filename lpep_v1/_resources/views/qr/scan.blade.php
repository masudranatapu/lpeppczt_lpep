@extends('layouts.dashboard')
@section('title', 'QR Code Scan')
@section('content')
<div class="max-w-xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Scan User QR</h1>

  <div id="reader" class="w-full"></div>
  <div id="scan-status" class="mt-4 text-sm text-gray-600"></div>
  <div id="error-box" class="mt-2 text-sm text-red-600"></div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://unpkg.com/html5-qrcode" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const statusEl = document.getElementById('scan-status');
  const errorEl  = document.getElementById('error-box');

  function postVerify(payload) {
    statusEl.textContent = 'Decoding...';
    errorEl.textContent  = '';

    fetch("{{ route('qr.verify') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ payload })
    })
    .then(r => r.json().then(j => ({ ok: r.ok, data: j })))
    .then(({ ok, data }) => {
      console.log(data);
      if (!ok || !data.ok) {
        errorEl.textContent = data.message || 'Verification failed';
        statusEl.textContent = '';
        html5QrcodeScanner.resume(); // allow another scan
        return;
      }
      statusEl.textContent = 'Matched! Redirecting...';
      window.location.href = data.redirect;
    })
    .catch(() => {
      errorEl.textContent = 'Network error';
      statusEl.textContent = '';
      html5QrcodeScanner.resume();
    });
  }

  function onScanSuccess(decodedText) {
    statusEl.textContent = 'Scanned. Sending to server...';
    html5QrcodeScanner.pause(); // pause to avoid duplicate posts
    postVerify(decodedText);
  }

  function onScanError(_) {}

  const html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: { width: 400, height: 400 } },
    false
  );
  html5QrcodeScanner.render(onScanSuccess, onScanError);
});
</script>
@endsection
