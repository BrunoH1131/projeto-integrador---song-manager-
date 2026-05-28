<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "cliente") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_reserva = (int) $_POST["id_reserva"];
$id_cliente = (int) $_SESSION["id"];

// Só cancela se a reserva pertence a esse cliente e está pendente
$stmt = $con->prepare("
    UPDATE reservas SET status = 'cancelado'
    WHERE id_reserva = ? AND id_cliente = ? AND status = 'pendente'
");
$stmt->bind_param("ii", $id_reserva, $id_cliente);
$stmt->execute();

$_SESSION['msg_ok'] = $stmt->affected_rows > 0
    ? "Reserva cancelada com sucesso."
    : "Não foi possível cancelar esta reserva.";

header("Location: meus_agendamentos.php");
exit;
?>