<?php
session_start();
require 'baglan.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

if ($_POST) {
    $hizmet_id = $_POST['hizmet_id'];
    $tarih = $_POST['tarih'];
    $saat = $_POST['saat'];
    $musteri_id = $_SESSION['kullanici_id'];

    $kontrol = $db->prepare("SELECT * FROM randevular WHERE randevu_tarihi = ? AND randevu_saati = ? AND durum != 'iptal'");
    $kontrol->execute([$tarih, $saat]);

    if ($kontrol->rowCount() > 0) {
        $mesaj = "<div class='alert alert-danger'>Üzgünüz, seçtiğiniz tarih ve saat dolu. Lütfen başka bir zaman seçin.</div>";
    } else {
        $kaydet = $db->prepare("INSERT INTO randevular (musteri_id, hizmet_id, randevu_tarihi, randevu_saati) VALUES (?, ?, ?, ?)");
        $basarili = $kaydet->execute([$musteri_id, $hizmet_id, $tarih, $saat]);
        if ($basarili) {
            $mesaj = "<div class='alert alert-success'>Randevunuz başarıyla oluşturuldu!</div>";
        }
    }
}

$hizmetler_sorgusu = $db->query("SELECT * FROM hizmetler");
$hizmetler = $hizmetler_sorgusu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Randevu Al</title>
    <!-- Bootstrap CSS Linki -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } </style>
</head>
<body>

<!-- Üst Menü (Navbar) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">✂️ Güzellik Merkezi</a>
    <div class="d-flex">
      <a href="randevularim.php" class="btn btn-outline-light me-2">Randevularım</a>
      <?php if($_SESSION['rol'] == 'admin'): ?>
        <a href="admin.php" class="btn btn-warning me-2">Admin Paneli</a>
      <?php endif; ?>
      <a href="cikis.php" class="btn btn-danger">Çıkış Yap</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Yeni Randevu Al</h4>
                </div>
                <div class="card-body">
                    
                    <p>Hoşgeldin <b><?php echo $_SESSION['ad_soyad']; ?></b>, lütfen randevu bilgilerinizi seçin.</p>
                    
                    <?php if(isset($mesaj)) echo $mesaj; ?> <!-- Hata veya başarı mesajı burada çıkar -->

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Hizmet Seçin</label>
                            <select name="hizmet_id" class="form-select" required>
                                <option value="">-- Bir Hizmet Seçin --</option>
                                <?php foreach ($hizmetler as $hizmet): ?>
                                    <option value="<?php echo $hizmet['id']; ?>"><?php echo $hizmet['hizmet_adi'] . ' - ' . $hizmet['fiyat'] . ' TL'; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="tarih" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Saat</label>
                            <input type="time" name="saat" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Randevuyu Onayla</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>