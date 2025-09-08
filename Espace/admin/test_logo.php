<?php
$logo_path = realpath(__DIR__ . '/../../asset/images/lito.jpg');
echo "Chemin : $logo_path<br>";
echo "Existe : " . (file_exists($logo_path) ? 'Oui' : 'Non') . "<br>";
echo "Lisible : " . (is_readable($logo_path) ? 'Oui' : 'Non') . "<br>";
?>