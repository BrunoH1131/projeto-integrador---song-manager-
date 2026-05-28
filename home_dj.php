<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

// Pegar perfil do DJ
$id_usuario = $_SESSION["id"];
$sql = $con->prepare("SELECT * FROM perfis_dj WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$perfil = $sql->get_result()->fetch_assoc();

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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Song Manager - DJ <?php echo htmlspecialchars($_SESSION['nome']); ?></title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, Helvetica, sans-serif; background: #f5f5f5; color: #111; overflow-x: hidden; }

header {
    width: 100%; background: #ffffff; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px #0002;
}
header h2 { font-size: 26px; white-space: nowrap; }
nav { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
nav a { text-decoration: none; color: black; font-size: 16px; padding: 8px 16px; background: #28a745; border-radius: 8px; border: 1px solid #1e7e34; white-space: nowrap; }
nav a:hover { background: #1e7e34; color: #fff; }

.container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

.perfil-dj { display: flex; gap: 30px; align-items: flex-start; margin-bottom: 40px; flex-wrap: wrap; }
.perfil-dj img { width: 300px; height: auto; border-radius: 12px; box-shadow: 0 0 12px #0003; } /* aumentei a imagem */
.perfil-dj .info { max-width: 700px; }
.perfil-dj .info h3 { margin-bottom: 10px; }
.perfil-dj .info p { margin-bottom: 8px; }

table { width: 100%; border-collapse: collapse; margin-bottom: 40px; background: #fff; box-shadow: 0 0 12px #0001; border-radius: 10px; overflow: hidden; }
th, td { padding: 12px 15px; text-align: left; }
th { background-color: #28a745; color: #fff; font-weight: bold; }
tr:nth-child(even) { background-color: #f5f5f5; }
tr:hover { background-color: #e2f0e6; }

footer { text-align: center; padding: 30px; background: #e9ecef; color: #111; margin-top: 40px; }

@media (max-width: 700px) {
    header { flex-direction: column; gap: 15px; }
    nav { justify-content: center; }
    .perfil-dj { flex-direction: column; align-items: center; }
    .perfil-dj .info { max-width: 100%; }
}
</style>

</head>
<body>

<header>
    <h2>Song Manager</h2>

   <nav>
    <a href="dj_perfil.php">Meu Perfil</a>
    <a href="agendamentos_dj.php">Agendamentos</a>
    <a href="logout.php">Sair</a>

</nav>
</header>

<div class="container">

    <!-- PERFIL DJ RESUMIDO -->
    <section class="perfil-dj">
        <?php if (!empty($perfil['foto'])): ?>
            <img src="uploads/<?php echo $perfil['foto']; ?>" alt="Foto DJ">
        <?php endif; ?>
        <div class="info">
            <h3><?php echo htmlspecialchars($_SESSION['nome']); ?></h3>
            <?php
// Busca o nome da categoria do DJ
$cat_sql = $con->prepare("SELECT c.nome FROM categorias_dj c JOIN perfis_dj p ON p.id_categoria = c.id_categoria WHERE p.id_usuario = ?");
$cat_sql->bind_param("i", $id_usuario);
$cat_sql->execute();
$cat = $cat_sql->get_result()->fetch_assoc();
?>
<p><strong>Estilo Musical:</strong> <?php echo htmlspecialchars($cat['nome'] ?? 'Não informado'); ?></p>
            <p><strong>Preço por evento:</strong> R$ <?php echo number_format($perfil['preco'], 2, ',', '.'); ?></p>
            <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($perfil['bio'])); ?></p>
        </div>
    </section>

    <!-- AGENDAMENTOS -->
    <section id="agendamentos">
        <h2>Seus Agendamentos</h2>
        <?php if ($reservas->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $reservas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['cliente']); ?></td>
                    <td><?php echo htmlspecialchars($row['data_evento']); ?></td>
                    <td><?php echo substr($row['horario_ini'], 0, 5) . ' às ' . substr($row['horario_fim'], 0, 5); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Você ainda não possui reservas agendadas.</p>
        <?php endif; ?>
    </section>

</div>

<footer>
    © 2025 - Song Manager. Todos os direitos reservados.
</footer>

</body>
</html>
