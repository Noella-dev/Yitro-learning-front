<?php
require_once 'C:/xampp/htdocs/yitroLearning/vendor/tcpdf/tcpdf.php';
$logo_path = realpath(__DIR__ . '/../../asset/images/lito.jpg');
if (!$logo_path || !file_exists($logo_path)) {
    die("Logo introuvable : $logo_path");
}
if (!is_readable($logo_path)) {
    die("Logo non lisible : $logo_path");
}
$pdf = new TCPDF();
$pdf->AddPage();
try {
    $pdf->Image($logo_path, 90, 30, 30, 0, '', '', 'T', false, 300, '', false, false, 0);
    $pdf->Output('C:/xampp/htdocs/yitroLearning/Espace/admin/certificats/test_logo.pdf', 'F');
    echo "PDF généré avec logo.";
} catch (Exception $e) {
    die("Erreur TCPDF : " . $e->getMessage());
}
?>