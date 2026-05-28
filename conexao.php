<?php
$con = new mysqli(
    "sql305.infinityfree.com",
    "if0_41954061",
    "11614009SONG",
    "if0_41954061_sistema_djs"
);

if ($con->connect_error) {
    die("ERRO CONEXÃO: " . $con->connect_error);
}
?>
