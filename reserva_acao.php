<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'dj') {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['id_reserva'], $_POST['acao'])) {
    header("Location: agendamentos_dj.php");
    exit;
}

$id_reserva = (int) $_POST['id_reserva'];
$acao       = $_POST['acao'];

// Mapeia acao para o status correto do banco
$mapa = [
    'aprovar' => 'aprovado',
    'recusar' => 'recusado'
];

if (!array_key_exists($acao, $mapa)) {
    $_SESSION['msg_status'] = "Ação inválida.";
    header("Location: agendamentos_dj.php");
    exit;
}

$novo_status = $mapa[$acao];
$id_dj       = $_SESSION['id'];

// Só atualiza se a reserva realmente pertence a esse DJ
$stmt = $con->prepare("
    UPDATE reservas SET status = ? 
    WHERE id_reserva = ? AND id_dj = ?
");
$stmt->bind_param("sii", $novo_status, $id_reserva, $id_dj);
$stmt->execute();

$_SESSION['msg_status'] = ($stmt->affected_rows > 0)
    ? "Reserva " . $novo_status . " com sucesso!"
    : "Nenhuma alteração realizada.";

header("Location: agendamentos_dj.php");
exit;
?>