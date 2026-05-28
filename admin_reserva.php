<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require "conexao.php";

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = $con->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $del->bind_param("i", $id);
    $del->execute();
    header("Location: admin_reservas.php");
    exit;
}

// UPDATE STATUS
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id = $_GET['id'];
    $upd = $con->prepare("UPDATE reservas SET status = ? WHERE id_reserva = ?");
    $upd->bind_param("si", $status, $id);
    $upd->execute();
    header("Location: admin_reservas.php");
    exit;
}

// LISTAR RESERVAS
$sql = "SELECT r.id_reserva, u.nome AS cliente, d.nome AS dj, r.data_evento, r.horario, r.status, r.local_evento, r.tipo_evento
        FROM reservas r
        JOIN usuarios u ON r.id_cliente = u.id_usuario
        JOIN usuarios d ON r.id_dj = d.id_usuario
        ORDER BY r.data_reserva DESC";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar Reservas - Admin</title>
<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

/* Caixa principal */
.box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow-x: auto;
}

/* Tabela bonita */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px 15px;
    text-align: left;
}

th {
    background-color: #28a745;
    color: #fff;
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #e6ffe6;
}

/* Botões */
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    margin-right: 5px;
}

.btn.aprovar { background-color: #28a745; }
.btn.aprovar:hover { background-color: #1e7e34; }

.btn.recusar { background-color: #dc3545; }
.btn.recusar:hover { background-color: #a71d2a; }

.btn.excluir { background-color: #6c757d; }
.btn.excluir:hover { background-color: #495057; }

.btn.home { 
    display: inline-block;
    margin-bottom: 20px;
    background-color: #007bff; 
}
.btn.home:hover { background-color: #0056b3; }

@media (max-width: 768px) {
    .box { padding: 15px; }
    th, td { font-size: 14px; }
}
</style>
</head>
<body>

<div class="container">
    <a href="home_admin.php" class="btn home">Voltar para Home</a>
    <h2>Gerenciar Reservas</h2>
    
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>DJ</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Local</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_reserva']; ?></td>
                    <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                    <td><?php echo htmlspecialchars($row['dj']); ?></td>
                    <td><?php echo $row['data_evento']; ?></td>
                    <td><?php echo $row['horario']; ?></td>
                    <td><?php echo $row['local_evento']; ?></td>
                    <td><?php echo $row['tipo_evento']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <?php if($row['status'] == 'pendente'): ?>
                            <a href="?id=<?php echo $row['id_reserva']; ?>&status=aprovado" class="btn aprovar">Aprovar</a>
                            <a href="?id=<?php echo $row['id_reserva']; ?>&status=recusado" class="btn recusar">Recusar</a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $row['id_reserva']; ?>" class="btn excluir" onclick="return confirm('Tem certeza que deseja excluir esta reserva?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
