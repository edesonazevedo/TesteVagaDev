<?php
    require_once('arquivo-class.php');
    $arquivo = new Arquivo();
    echo json_encode($arquivo->veiricarDiretorio());
?>