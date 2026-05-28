<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "cliente") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_cliente  = (int) $_SESSION["id"];
$id_dj       = (int) $_POST["id_dj"];
$data_evento = $_POST["data_evento"];
$horario_ini = $_POST["horario"];
$horario_fim = $_POST["horario_fim"] ?? '23:59:00'; // campo novo do banco
$status      = "pendente";

// Verifica conflito de horário antes de inserir
$verifica = $con->prepare("
    SELECT id_reserva FROM reservas 
    WHERE id_dj = ? AND data_evento = ? AND horario_ini = ?
");
$verifica->bind_param("iss", $id_dj, $data_evento, $horario_ini);
$verifica->execute();
$verifica->store_result();

if ($verifica->num_rows > 0) {
    $_SESSION['msg_erro'] = "Este DJ já tem reserva nesse horário. Escolha outro.";
    header("Location: agendar_dj.php?id=" . $id_dj);
    exit;
}

$insert = $con->prepare("
    INSERT INTO reservas 
    (id_cliente, id_dj, data_evento, horario_ini, horario_fim, status, local_evento, tipo_evento, observacoes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$insert->bind_param(
    "iissssss s",
    $id_cliente, $id_dj, $data_evento,
    $horario_ini, $horario_fim, $status,
    $_POST['local_evento'], $_POST['tipo_evento'],
    $_POST['observacoes'] ?? ''
);

if ($insert->execute()) {
    $_SESSION['msg_ok'] = "Reserva realizada! Aguarde a confirmação do DJ.";
    header("Location: home_cliente.php");
} else {
    $_SESSION['msg_erro'] = "Erro ao agendar: " . $con->error;
    header("Location: agendar_dj.php?id=" . $id_dj);
}
exit;
?>