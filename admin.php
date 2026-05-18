<?php
session_start();
require 'baglan.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['islem']) && isset($_GET['id'])) {
    $islem = $_GET['islem'];
    $randevu_id = $_GET['id'];
    $yeni_durum = '';

    if ($islem == 'onayla') $yeni_durum = 'onaylandi';
    elseif ($islem == 'iptal') $yeni_durum = 'iptal';

    if ($yeni_durum != '') {
        $guncelle = $db->prepare("UPDATE randevular SET durum = ? WHERE id = ?");
        $guncelle->execute([$yeni_durum, $randevu_id]);
        header("Location: admin.php");
        exit;
    }
}

$sorgu = $db->query("
    SELECT randevular.*, kullanicilar.ad_soyad, hizmetler.hizmet_adi 
    FROM randevular 
    INNER JOIN kullanicilar ON randevular.musteri_id = kullanicilar.id
    INNER JOIN hizmetler ON randevular.hizmet_id = hizmetler.id
    ORDER BY randevular.randevu_tarihi DESC, randevular.randevu_saati DESC
");
$tum_randevular = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Paneli</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin.php">👑 Yönetici Paneli</a>
    <div class="d-flex">
      <a href="index.php" class="btn btn-outline-light me-2">Ana Siteye Dön</a>
      <a href="cikis.php" class="btn btn-danger">Çıkış Yap</a>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4 px-4">
    <h3 class="mb-4">Tüm Sistem Randevuları</h3>
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Müşteri Adı</th>
                            <th>Hizmet</th>
                            <th>Tarih</th>
                            <th>Saat</th>
                            <th>Durum</th>
                            <th>İşlem (Yönetim)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tum_randevular as $r): ?>
                            <tr>
                                <td class="fw-bold text-start ps-3"><?php echo $r['ad_soyad']; ?></td>
                                <td><?php echo $r['hizmet_adi']; ?></td>
                                <td><?php echo date('d.m.Y', strtotime($r['randevu_tarihi'])); ?></td>
                                <td><?php echo date('H:i', strtotime($r['randevu_saati'])); ?></td>
                                <td>
                                    <?php 
                                    if($r['durum'] == 'bekliyor') echo "<span class='badge bg-warning text-dark'>⏳ Bekliyor</span>";
                                    if($r['durum'] == 'onaylandi') echo "<span class='badge bg-success'>✅ Onaylandı</span>";
                                    if($r['durum'] == 'iptal') echo "<span class='badge bg-danger'>❌ İptal</span>";
                                    ?>
                                </td>
                                <td>
                                    <?php if($r['durum'] == 'bekliyor'): ?>
                                        <a href="admin.php?islem=onayla&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-success">Onayla</a>
                                        <a href="admin.php?islem=iptal&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-danger">İptal Et</a>
                                    <?php else: ?>
                                        <span class="text-muted small">İşlem Tamamlandı</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>