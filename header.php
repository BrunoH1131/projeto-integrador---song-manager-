<?php
// Conta notificações não lidas do usuário logado
$notif_count = 0;
if (isset($_SESSION["id"])) {
    $nq = $con->prepare("SELECT COUNT(*) FROM notificacoes WHERE id_usuario = ? AND lida = 0");
    $nq->bind_param("i", $_SESSION["id"]);
    $nq->execute();
    $nq->bind_result($notif_count);
    $nq->fetch();
    $nq->close();
}
?>

<style>
.notif-badge {
    display:inline-flex;
    align-items:center;
    gap:6px;
    text-decoration:none;
    color:black;
    font-size:16px;
    padding:8px 16px;
    background:#28a745;
    border-radius:8px;
    border:1px solid #1e7e34;
    margin-left:10px;
}
.notif-badge:hover { background:#1e7e34; color:#fff; }
.badge-count {
    background:#dc3545;
    color:#fff;
    border-radius:50%;
    font-size:11px;
    font-weight:bold;
    width:18px;
    height:18px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
</style>

<a href="notificacoes.php" class="notif-badge">
    🔔
    <?php if ($notif_count > 0): ?>
        <span class="badge-count"><?php echo $notif_count > 9 ? '9+' : $notif_count; ?></span>
    <?php endif; ?>
</a>