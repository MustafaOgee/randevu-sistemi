<?php
session_start();
require 'baglan.php';

// Zaten giriş yapmış biriyse, tekrar giriş sayfasını göremesin, ana sayfaya yollayalım
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $sorgu->execute([$email]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC); 

    if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
        $_SESSION['kullanici_id'] = $kullanici['id'];
        $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
        $_SESSION['rol'] = $kullanici['rol'];
        header("Location: index.php"); // Başarılı girişte doğrudan index.php'ye at
        exit;
    } else {
        $hata = "<div class='alert alert-danger'>Hata: E-posta veya şifre yanlış!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> 
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white text-center py-3">
        <h4 class="mb-0">✂️ Sisteme Giriş</h4>
    </div>
    <div class="card-body p-4">
        
        <?php if(isset($hata)) echo $hata; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">E-posta Adresi</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="ornek@mail.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted fw-bold">Şifre</label>
                <input type="password" name="sifre" class="form-control form-control-lg" placeholder="******" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg w-100">Giriş Yap</button>
        </form>
        
    </div>
    <div class="card-footer text-center bg-white py-3">
        <span class="text-muted">Hesabınız yok mu?</span> <a href="kayit.php" class="text-decoration-none fw-bold">Hemen Kayıt Ol</a>
    </div>
</div>

</body>
</html>