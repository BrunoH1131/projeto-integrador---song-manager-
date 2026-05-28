<?php
session_start();
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require "conexao.php";

// DELETE DJ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $del = $con->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND tipo = 'dj'");
    $del->bind_param("i", $id);
    $del->execute();
    header("Location: admin_djs.php");
    exit;
}

// LISTAR DJs
$sql = "SELECT id_usuario, nome, email, data_cadastro FROM usuarios WHERE tipo = 'dj' ORDER BY id_usuario DESC";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar DJs - Admin</title>
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

.box {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow-x: auto;
}

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

.btn.editar { background-color: #007bff; }
.btn.editar:hover { background-color: #0056b3; }

.btn.excluir { background-color: #dc3545; }
.btn.excluir:hover { background-color: #a71d2a; }

.btn.home { 
    display: inline-block;
    margin-bottom: 20px;
    background-color: #6c757d; 
}
.btn.home:hover { background-color: #495057; }

@media (max-width: 768px) {
    .box { padding: 15px; }
    th, td { font-size: 14px; }
}
</style>
</head>
<body>

<div class="container">
    <a href="home_admin.php" class="btn home">Voltar para Home</a>
    <h2>Gerenciar DJs</h2>
    
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Data Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_usuario']; ?></td>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo $row['data_cadastro']; ?></td>
                    <td>
                        <a href="admin_djs_editar.php?id=<?php echo $row['id_usuario']; ?>" class="btn editar">Editar</a>
                        <a href="?delete=<?php echo $row['id_usuario']; ?>" class="btn excluir" onclick="return confirm('Deseja realmente excluir este DJ?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
