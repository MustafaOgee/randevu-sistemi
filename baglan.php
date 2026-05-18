<?php
$host = 'localhost';
$veritabani_adi = 'randevu_sistemi';
$kullanici_adi = 'root';
$sifre = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$veritabani_adi;charset=utf8", $kullanici_adi, $sifre);
} catch (PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}
?>