<?php
$signature_path = 'C:/xampp/htdocs/yitroLearning/asset/images/signature.jpg';
echo "Chemin : $signature_path<br>";
echo "Existe : " . (file_exists($signature_path) ? 'Oui' : 'Non') . "<br>";
echo "Lisible : " . (is_readable($signature_path) ? 'Oui' : 'Non') . "<br>";
?>