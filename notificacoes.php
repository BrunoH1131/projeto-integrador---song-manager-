<?php
session_start();

if (!isset($_SESSION["tipo"])) {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_usuario = (int) $_SESSION["id"];

// Marca todas como lidas ao abrir a página
$con->prepare("UPDATE notificacoes SET lida = 1 WHERE id_usuario = ?")
    ->bind_param("i", $id_usuario) || null;
$stmt = $con->prepare("UPDATE notificacoes SET lida = 1 WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

// Busca todas as notificações
$sql = $con->prepare("
    SELECT mensagem, lida, data_criacao 
    FROM notificacoes 
    WHERE id_usuario = ? 
    ORDER BY data_criacao DESC
");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$notifs = $sql->get_result();

// Define para onde voltar conforme tipo
$home = match($_SESSION["tipo"]) {
    "cliente" => "home_cliente.php",
    "dj"      => "home_dj.php",
    "admin"   => "home_admin.php",
    default   => "login.php"
};
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Notificações - Song Manager</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; color:#111; }

header { width:100%; background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:26px; }
nav a { text-decoration:none; color:black; font-size:16px; padding:8px 16px; background:#28a745; border-radius:8px; border:1px solid #1e7e34; margin-left:10px; }
nav a:hover { background:#1e7e34; color:#fff; }

.container { max-width:800px; margin:40px auto; padding:0 20px; }
.container h2 { margin-bottom:25px; }

.notif {
    background:#fff;
    border-radius:10px;
    padding:16px 20px;
    margin-bottom:12px;
    box-shadow:0 0 8px #0001;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    border-left:4px solid #28a745;
}

.notif.nao-lida {
    border-left-color:#ffc107;
    background:#fffdf0;
}

.notif .mensagem { font-size:15px; flex:1; }
.notif .data { font-size:13px; color:#888; white-space:nowrap; }

.vazio { text-align:center; padding:60px 20px; color:#888; font-size:18px; }

footer { text-align:center; padding:30px; background:#e9ecef; margin-top:40px; }
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
        <a href="<?php echo $home; ?>">Voltar</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Notificações</h2>

    <?php if ($notifs->num_rows === 0): ?>
        <div class="vazio">Nenhuma notificação por enquanto.</div>
    <?php else: ?>
        <?php while ($n = $notifs->fetch_assoc()): ?>
        <div class="notif <?php echo $n['lida'] ? '' : 'nao-lida'; ?>">
            <span class="mensagem"><?php echo htmlspecialchars($n['mensagem']); ?></span>
            <span class="data"><?php echo date('d/m/Y H:i', strtotime($n['data_criacao'])); ?></span>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer>© 2025 - Song Manager. Todos os direitos reservados.</footer>

</body>
</html>