<?php
session_start();
session_destroy();
echo "Tüm oturumlar temizlendi. Şimdi tekrar giriş yapmayı deneyin.";
?>