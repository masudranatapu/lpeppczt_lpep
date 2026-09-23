<div id="printableArea" class="text-center p-4">
    <h2 class="text-xl font-semibold mb-2">Customer QR Code</h2>
    <img id="qrImg" src="{{ $qrcode }}" alt="QR Code" style="max-width: 300px;">
    <h4 class="mt-3 font-bold">Customer Name: {{ $user->name }}</h4>
    <p class="mt-2">Scan to view customer details.</p>
    <button id="printButton" onclick="printDiv()" style="margin-top:10px;padding:6px 12px;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;">🖨 Print</button>
</div>

<style>
/* Make only the section print */
@media print {
  body * { visibility: hidden !important; }
  #printableArea, #printableArea * { visibility: visible !important; }
  #printableArea { position: absolute; left: 0; top: 0; width: 100%; }
  #printButton { display: none !important; }
  #printableArea img { max-width: 100%; height: auto; }
  /* Better color fidelity on some browsers */
  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>

<script>
function printDiv() {
  const img = document.querySelector('#printableArea img');
  // If the image hasn't finished loading, wait for it, then print
  if (img && !img.complete) {
    img.addEventListener('load', () => window.print(), { once: true });
    img.addEventListener('error', () => window.print(), { once: true }); // fallback
  } else {
    window.print();
  }
}
</script>
