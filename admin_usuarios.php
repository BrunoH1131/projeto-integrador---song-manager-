<?php
session_start();
if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "admin") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

// EXCLUIR USUÁRIO
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_usuarios.php");
    exit;
}

// BUSCAR USUÁRIOS
$result = $con->query("SELECT id_usuario, nome, email, tipo, data_cadastro FROM usuarios ORDER BY id_usuario ASC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar Usuários - Admin</title>
<style>
body { font-family: Arial; background:#f5f5f5; color:#111; margin:0; padding:0; }
header { background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:26px; }
nav a { text-decoration:none; color:#fff; background:#28a745; padding:8px 16px; border-radius:8px; margin-left:10px; }
.container { max-width:1200px; margin:40px auto; padding:0 20px; }
table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 0 12px #0001; border-radius:10px; overflow:hidden; }
th, td { padding:12px 15px; text-align:left; }
th { background:#28a745; color:#fff; }
tr:nth-child(even) { background:#f5f5f5; }
tr:hover { background:#e2f0e6; }
.btn { text-decoration:none; padding:6px 12px; border-radius:6px; color:#fff; transition:0.3s; }
.edit { background:#007bff; }
.edit:hover { background:#0056b3; }
.delete { background:#dc3545; }
.delete:hover { background:#a71d2a; }
.add { display:inline-block; margin-bottom:10px; background:#28a745; }
.add:hover { background:#1e7e34; }
</style>
</head>
<body>

<header>
    <h2>Administrador</h2>
    <nav>
        <a href="home_admin.php">Home</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Gerenciar Usuários</h2>
    <a href="admin_usuario_adicionar.php" class="btn add">Adicionar Usuário</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
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
                    <td><?php echo $row['tipo']; ?></td>
                    <td><?php echo $row['data_cadastro']; ?></td>
                    <td>
                        <a href="admin_usuario_editar.php?id=<?php echo $row['id_usuario']; ?>" class="btn edit">Editar</a>
                        <a href="admin_usuarios.php?delete=<?php echo $row['id_usuario']; ?>" class="btn delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
