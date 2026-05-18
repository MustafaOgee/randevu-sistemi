<?php
// Oturumu başlat (Hafızaya erişmek için)
session_start();

// Hafızadaki tüm kullanıcı bilgilerini (Session'ları) yok et
session_destroy();

// Kullanıcıyı tekrar giriş sayfasına şutla
header("Location: giris.php");
exit;
?>