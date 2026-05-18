<?php
session_start();
require 'baglan.php';

// Güvenlik Duvarı
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit;
}

$musteri_id = $_SESSION['kullanici_id'];

// Müşterinin randevularını hizmet adlarıyla birlikte çekiyoruz
$sorgu = $db->prepare("
    SELECT randevular.*, hizmetler.hizmet_adi, hizmetler.fiyat 
    FROM randevular 
    INNER JOIN hizmetler ON randevular.hizmet_id = hizmetler.id 
    WHERE randevular.musteri_id = ?
    ORDER BY randevular.randevu_tarihi DESC, randevular.randevu_saati DESC
");
$sorgu->execute([$musteri_id]);
$randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Geçmiş Randevularım</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Üst Menü (Navbar) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">✂️ Güzellik Merkezi</a>
    <div class="d-flex">
      <a href="index.php" class="btn btn-outline-light me-2">⬅ Ana Sayfaya Dön</a>
      <a href="cikis.php" class="btn btn-danger">Çıkış Yap</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h3 class="mb-4 text-center text-secondary"><b><?php echo $_SESSION['ad_soyad']; ?></b>, İşte Randevu Geçmişin</h3>
    
    <?php if (count($randevular) > 0): ?>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Hizmet Adı</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($randevular as $randevu): ?>
                                <tr>
                                    <td class="fw-bold text-start ps-3"><?php echo $randevu['hizmet_adi']; ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($randevu['randevu_tarihi'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($randevu['randevu_saati'])); ?></td>
                                    <td><?php echo $randevu['fiyat']; ?> TL</td>
                                    <td>
                                        <?php 
                                        if($randevu['durum'] == 'bekliyor') echo "<span class='badge bg-warning text-dark'>⏳ Bekliyor</span>";
                                        if($randevu['durum'] == 'onaylandi') echo "<span class='badge bg-success'>✅ Onaylandı</span>";
                                        if($randevu['durum'] == 'iptal') echo "<span class='badge bg-danger'>❌ İptal Edildi</span>";
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center fs-5 mt-4">
            Henüz sistemde kayıtlı bir randevunuz bulunmamaktadır. <br>
            <a href="index.php" class="btn btn-primary mt-3">Hemen Randevu Al</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>