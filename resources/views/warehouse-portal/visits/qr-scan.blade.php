<x-warehouse-layout title="QR Code Scan">
    <div class="mx-auto max-w-2xl rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="mb-5"><h1 class="m-0 text-xl font-extrabold text-slate-900">QR Code Scan</h1><p class="mb-0 mt-1 text-sm text-slate-500">Scan a beneficiary QR code to open a new visit entry.</p></div>
        <div id="reader" class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50"></div>
        <p id="scan-status" class="mb-0 mt-4 text-sm font-semibold text-slate-600"></p><p id="error-box" class="mb-0 mt-2 text-sm text-rose-600"></p>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusEl = document.getElementById('scan-status'), errorEl = document.getElementById('error-box');
            function verify(payload) { statusEl.textContent = 'Decoding...'; errorEl.textContent = ''; fetch("{{ route('warehouse.qr.verify') }}", { method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}, body: JSON.stringify({payload}) }).then(response => response.json().then(data => ({ok: response.ok, data}))).then(({ok,data}) => { if (!ok || !data.ok) { errorEl.textContent = data.message || 'Verification failed'; statusEl.textContent = ''; scanner.resume(); return; } statusEl.textContent = 'Matched. Opening visit entry...'; window.location.href = data.redirect; }).catch(() => { errorEl.textContent = 'Network error'; statusEl.textContent = ''; scanner.resume(); }); }
            function success(text) { scanner.pause(); verify(text); }
            const scanner = new Html5QrcodeScanner('reader', {fps: 10, qrbox: {width: 280, height: 280}}, false); scanner.render(success, () => {});
        });
    </script>
</x-warehouse-layout>
