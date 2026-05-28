
<?php
session_start();

echo $_SESSION["tipo"];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Admin - Home</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif; }
body { background:#f5f5f5; color:#111; }
header { width:100%; background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:28px; }
nav a { text-decoration:none; color:#fff; font-size:16px; padding:8px 16px; background:#28a745; border-radius:8px; margin-left:10px; }
nav a:hover { background:#1e7e34; }

.container { max-width:1200px; margin:40px auto; padding:0 20px; text-align:center; }
h2 { margin-bottom:20px; }

button { padding:15px 30px; font-size:18px; margin:10px; cursor:pointer; border:none; border-radius:10px; background:#28a745; color:#fff; transition:0.3s; }
button:hover { background:#1e7e34; }

footer { text-align:center; padding:30px; background:#e9ecef; color:#111; margin-top:40px; }
</style>
</head>
<body>

<header>
    <h2>Song Manager - Administrador</h2>
    <nav>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Bem-vindo, Administrador!</h2>
    <p>Gerencie o sistema utilizando os botões abaixo:</p>

    <a href="admin_usuarios.php"><button>Gerenciar Usuários</button></a>
    <a href="admin_reserva.php"><button>Gerenciar Reservas</button></a>
    <a href="admin_djs.php"><button>Gerenciar DJs</button></a>
</div>

<footer>
    © 2025 - Song Manager. Todos os direitos reservados.
</footer>

</body>
</html>
