<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_dj       = $_SESSION["id"];
$id_cliente  = $_POST["id_cliente"];
$data_evento = $_POST["data_evento"];
$horario     = $_POST["horario"];
$status      = "pendente"; // status inicial
$data_reserva = date("Y-m-d H:i:s");

// Verificar se já existe reserva nesse DJ no mesmo dia/hora
$verifica = $con->prepare("SELECT id_reserva FROM reservas WHERE id_dj=? AND data_evento=? AND horario=?");
$verifica->bind_param("iss", $id_dj, $data_evento, $horario);
$verifica->execute();
$verifica->store_result();

if ($verifica->num_rows > 0) {
    echo "Este DJ já possui uma reserva nesse horário. Escolha outro.";
    exit;
}

// Inserir reserva
$insert = $con->prepare("INSERT INTO reservas (id_cliente, id_dj, data_evento, horario, status, data_reserva) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("iissss", $id_cliente, $id_dj, $data_evento, $horario, $status, $data_reserva);
$insert->execute();

header("Location: agendamentos_dj.php");
exit;
?>
