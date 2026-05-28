<?php
session_start();
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_usuario = (int) $_SESSION["id"];

// Pegar reservas do DJ
$res_sql = $con->prepare("
    SELECT r.id_reserva, u.nome AS cliente, r.data_evento, 
           r.horario_ini, r.horario_fim, r.status, 
           r.local_evento, r.tipo_evento
    FROM reservas r
    JOIN usuarios u ON r.id_cliente = u.id_usuario
    WHERE r.id_dj = ?
    ORDER BY r.data_evento ASC
");
$res_sql->bind_param("i", $id_usuario);
$res_sql->execute();
$reservas = $res_sql->get_result();

// Mensagem de status após ação
$msg_status = $_SESSION['msg_status'] ?? '';
unset($_SESSION['msg_status']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Agendamentos DJ</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, Helvetica, sans-serif; background: #f5f5f5; color: #111; overflow-x: hidden; }

header { width: 100%; background: #ffffff; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px #0002; }
header h2 { font-size: 26px; white-space: nowrap; }
nav { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
nav a { text-decoration: none; color: black; font-size: 16px; padding: 8px 16px; background: #28a745; border-radius: 8px; border: 1px solid #1e7e34; white-space: nowrap; }
nav a:hover { background: #1e7e34; color: #fff; }

.container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

.msg-ok   { background:#d4edda; color:#155724; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold; }
.msg-erro { background:#f8d7da; color:#721c24; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold; }

table { width: 100%; border-collapse: collapse; margin-bottom: 40px; background: #fff; box-shadow: 0 0 12px #0001; border-radius: 10px; overflow: hidden; }
th, td { padding: 12px 15px; text-align: left; }
th { background-color: #28a745; color: #fff; font-weight: bold; }
tr:nth-child(even) { background-color: #f5f5f5; }
tr:hover { background-color: #e2f0e6; }

button { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold; }
button.aprovar { background-color: #28a745; color: #fff; }
button.recusar { background-color: #dc3545; color: #fff; }
button.aprovar:hover { background-color: #1e7e34; }
button.recusar:hover { background-color: #b02a37; }

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}
.badge.pendente  { background:#fff3cd; color:#856404; }
.badge.aprovado  { background:#d4edda; color:#155724; }
.badge.recusado  { background:#f8d7da; color:#721c24; }
.badge.cancelado { background:#e2e3e5; color:#383d41; }

footer { text-align: center; padding: 30px; background: #e9ecef; color: #111; margin-top: 40px; }

@media (max-width: 700px) {
    header { flex-direction: column; gap: 15px; }
    nav { justify-content: center; }
}
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
        <a href="home_dj.php">Início</a>
        <a href="dj_perfil.php">Meu Perfil</a>
        <a href="agendamentos_dj.php">Agendamentos</a>
        <?php include "header.php"; ?>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Seus Agendamentos</h2>

    <?php if ($msg_status): ?>
        <div class="msg-ok"><?php echo htmlspecialchars($msg_status); ?></div>
    <?php endif; ?>

    <?php if ($reservas->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Data</th>
                <th>Horário</th>
                <th>Local</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $reservas->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['data_evento'])); ?></td>
                <td><?php echo substr($row['horario_ini'], 0, 5) . ' às ' . substr($row['horario_fim'], 0, 5); ?></td>
                <td><?php echo htmlspecialchars($row['local_evento'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['tipo_evento'] ?? '-'); ?></td>
                <td>
                    <span class="badge <?php echo $row['status']; ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
                <td>
                    <?php if($row['status'] === 'pendente'): ?>
                    <form action="reserva_acao.php" method="POST" style="display:inline">
                        <input type="hidden" name="id_reserva" value="<?php echo $row['id_reserva']; ?>">
                        <input type="hidden" name="acao" value="aprovar">
                        <button type="submit" class="aprovar">Aprovar</button>
                    </form>
                    <form action="reserva_acao.php" method="POST" style="display:inline">
                        <input type="hidden" name="id_reserva" value="<?php echo $row['id_reserva']; ?>">
                        <input type="hidden" name="acao" value="recusar">
                        <button type="submit" class="recusar">Recusar</button>
                    </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Você ainda não possui reservas agendadas.</p>
    <?php endif; ?>
</div>

<footer>
    © 2025 - Song Manager. Todos os direitos reservados.
</footer>

</body>
</html>