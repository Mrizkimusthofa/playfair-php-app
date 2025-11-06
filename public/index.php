<?php
session_start();
require_once __DIR__ . '/../src/functions.php';
$result = $_SESSION['result'] ?? null;
$processLogSession = $_SESSION['processLog'] ?? null;
unset($_SESSION['result'], $_SESSION['processLog']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Playfair Cipher — Demo UTS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-9">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-3">Playfair Cipher — Demo</h3>
          <p class="text-muted">Masukkan passphrase (kunci), pesan, lalu pilih Enkripsi atau Dekripsi.</p>

          <form id="mainForm" action="action.php" method="post">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Passphrase (kunci)</label>
                <input name="key" class="form-control" placeholder="contoh: TEKNOLOGI" required>
                <div class="form-text">Huruf J akan digabung dengan I. Non-alfabet diabaikan.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Mode</label>
                <select name="mode" class="form-select">
                  <option value="encrypt">Enkripsi</option>
                  <option value="decrypt">Dekripsi</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Pesan</label>
                <textarea name="text" rows="6" class="form-control" placeholder="Masukkan pesan..." required></textarea>
              </div>

              <div class="col-12">
                <button class="btn btn-primary" type="submit">Proses</button>
                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
              </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3 mt-3" onclick="loadExample()">Load Example</button>
          </form>
<script>
function loadExample(){
 document.querySelector('textarea[name="text"]').value = 'HELLO WORLD';
 document.querySelector('input[name="key"]').value = 'CRYPTOLOGY';
}
</script>

          <hr>

          <h5>Hasil</h5>
          <?php if($result): ?>
            <div class="mb-2">
              <label class="form-label">Matrix Kunci (5×5)</label>
              <pre class="p-3 bg-dark text-light rounded"><?php echo htmlspecialchars($result['matrix_display']); ?></pre>
            </div>
            <div class="mb-2">
              <label class="form-label">Output (ciphertext / plaintext)</label>
              <pre id="out" class="p-3 bg-white border rounded"><?php echo htmlspecialchars($result['output']); ?></pre>
            </div>
            <button class="btn btn-sm btn-outline-success mb-3" onclick="copyOut()">Copy Output</button>
          <?php else: ?>
            <div class="alert alert-info">Hasil akan muncul di sini setelah kamu klik Proses.</div>
          <?php endif; ?>

        </div>
      </div>

      <div class="mt-3 text-muted small">
        <p><strong>Catatan:</strong> Saat enkripsi, jika pasangan huruf sama, akan ditambahkan huruf 'X' di antara pasangan. Jika panjang ganjil, akan dipadding 'X' di akhir.</p>
      </div>

      <!-- Process log viewer -->
      <div class="mt-4">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span>Detail Proses Algoritma</span>
            <button class="btn btn-sm btn-link" id="toggleLogBtn" type="button" data-bs-toggle="collapse" data-bs-target="#processCard" aria-expanded="true">Toggle</button>
          </div>
          <div id="processCard" class="collapse show">
            <div class="card-body">
              <?php if($processLogSession): ?>
                <pre id="processLog" class="small text-monospace p-2 bg-light border rounded"><?php echo htmlspecialchars($processLogSession); ?></pre>
              <?php else: ?>
                <pre id="processLog" class="small text-monospace p-2 bg-light border rounded">Tidak ada log proses. Jalankan proses terlebih dahulu.</pre>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="mt-2">
          <button class="btn btn-sm btn-outline-primary" id="playStepsBtn">Play Steps</button>
          <button class="btn btn-sm btn-outline-secondary" id="stopStepsBtn" style="display:none">Stop</button>
        </div>
      </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
function copyOut(){
  const text = document.getElementById('out')?.innerText || '';
  navigator.clipboard?.writeText(text).then(()=> alert('Output tersalin ke clipboard'));
}
// auto-scroll log to bottom on load
document.addEventListener('DOMContentLoaded', ()=> {
  const pl = document.getElementById('processLog');
  if(pl) pl.scrollTop = pl.scrollHeight;
});
</script>
</body>
</html>
