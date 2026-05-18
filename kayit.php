<?php
session_start();

// Zaten giriş yapmış biriyse, ana sayfaya yollayalım
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}

require 'baglan.php';

if ($_POST) {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];
    
    $sifreli_parola = password_hash($sifre, PASSWORD_DEFAULT);

    $kontrol = $db->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $kontrol->execute([$email]);
    
    if ($kontrol->rowCount() > 0) {
         $mesaj = "<div class='alert alert-danger'>Bu e-posta adresi zaten kullanılıyor!</div>";
    } else {
        $ekle = $db->prepare("INSERT INTO kullanicilar (ad_soyad, email, sifre) VALUES (?, ?, ?)");
        $sonuc = $ekle->execute([$ad_soyad, $email, $sifreli_parola]);
        
        if ($sonuc) {
            $mesaj = "<div class='alert alert-success'>Kayıt başarılı! Lütfen giriş yapın.</div>";
        } else {
            $mesaj = "<div class='alert alert-danger'>Kayıt sırasında bir hata oluştu.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> 
        body { background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { width: 100%; max-width: 450px; }
    </style>
</head>
<body>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white text-center py-3">
        <h4 class="mb-0">Yeni Hesap Oluştur</h4>
    </div>
    <div class="card-body p-4">
        
        <?php if(isset($mesaj)) echo $mesaj; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted fw-bold">Ad Soyad</label>
                <input type="text" name="ad_soyad" class="form-control form-control-lg" placeholder="Örn: Ali Yılmaz" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-bold">E-posta Adresi</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="ornek@mail.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted fw-bold">Şifre Belirleyin</label>
                <input type="password" name="sifre" class="form-control form-control-lg" placeholder="******" required>
            </div>
            
            <button type="submit" class="btn btn-success btn-lg w-100">Kayıt Ol</button>
        </form>
        
    </div>
    <div class="card-footer text-center bg-white py-3">
        <span class="text-muted">Zaten hesabınız var mı?</span> <a href="giris.php" class="text-decoration-none fw-bold">Giriş Yap</a>
    </div>
</div>

</body>
</html>