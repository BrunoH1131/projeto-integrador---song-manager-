<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "cliente") {
    header("Location: login.php");
    exit;
}
?>

require "conexao.php";

// Pegar lista de DJs para mostrar (opcional, pode ser usado no agendamento depois)
$djs_sql = $con->query("SELECT u.id_usuario, u.nome, c.nome AS estilo_musical, p.preco, p.foto, p.avaliacao_media
                        FROM usuarios u 
                        JOIN perfis_dj p ON u.id_usuario = p.id_usuario
                        LEFT JOIN categorias_dj c ON c.id_categoria = p.id_categoria
                        WHERE u.tipo = 'dj' AND u.ativo = 1");
                     
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Song Manager - Cliente</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; color:#111; overflow-x:hidden; }
header { width:100%; background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:26px; }
nav { display:flex; gap:15px; align-items:center; flex-wrap:wrap; }
nav a { text-decoration:none; color:black; font-size:16px; padding:8px 16px; background:#28a745; border-radius:8px; border:1px solid #1e7e34; white-space:nowrap; }
nav a:hover { background:#1e7e34; color:#fff; }

.hero { display:flex; align-items:center; justify-content:center; padding:60px 5%; max-width:1200px; margin:auto; gap:40px; flex-wrap:wrap; background:#fff; border-radius:12px; box-shadow:0 0 12px #0002; }
.hero img { width:100%; max-width:500px; height:auto; border-radius:12px; }
.hero-text { max-width:600px; }
.hero-text h1 { font-size:36px; margin-bottom:15px; }
.hero-text p { font-size:18px; line-height:1.5; }

.cards { display:flex; gap:20px; flex-wrap:wrap; margin-top:40px; justify-content:center; }
.card { background:#fff; border-radius:12px; box-shadow:0 0 12px #0001; padding:15px; width:250px; text-align:center; transition:0.3s; }
.card img { width:100%; border-radius:10px; margin-bottom:10px; }
.card:hover { transform:translateY(-5px); box-shadow:0 0 18px #0002; }
.card h3 { margin-bottom:5px; }
.card p { margin-bottom:10px; }
.card a { text-decoration:none; color:#fff; background:#28a745; padding:8px 12px; border-radius:8px; display:inline-block; }
.card a:hover { background:#1e7e34; }

footer { text-align:center; padding:30px; background:#e9ecef; color:#111; margin-top:40px; }

@media(max-width:700px) {
    header { flex-direction:column; gap:15px; }
    .hero { flex-direction:column; }
    .cards { flex-direction:column; align-items:center; }
}
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
    <a href="meus_agendamentos.php">Minhas Reservas</a>
    <?php include "header.php"; ?>
    <a href="logout.php">Sair</a>
    </nav>  
</header>

<div class="hero">
    <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?auto=format&fit=crop&w=1200&q=80" alt="Bem-vindo">
    <div class="hero-text">
        <h1>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
        <p>Bem-vindo ao Song Manager! Encontre DJs incríveis, agende seu evento e gerencie tudo com facilidade.</p>
    </div>
</div>

<div class="container">
    <h2 style="text-align:center; margin-top:40px;">DJs Disponíveis</h2>
    <div class="cards">
        <?php while($dj = $djs_sql->fetch_assoc()): ?>
        <div class="card">
        <?php if($dj['foto']): ?>
        <img src="uploads/<?php echo htmlspecialchars($dj['foto']); ?>" alt="<?php echo htmlspecialchars($dj['nome']); ?>">
        <?php else: ?>
        <img src="https://via.placeholder.com/250x200?text=DJ" alt="Sem foto">
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($dj['nome']); ?></h3>
        <p><strong>Estilo:</strong> <?php echo htmlspecialchars($dj['estilo_musical'] ?? 'Não informado'); ?></p>
        <p><strong>Preço:</strong> R$ <?php echo number_format($dj['preco'],2,',','.'); ?></p>
        <p><strong>Avaliação:</strong> 
        <?php 
        $media = $dj['avaliacao_media'];
        if ($media > 0) {
            echo number_format($media, 1) . ' ★';
        } else {
            echo 'Sem avaliações';
        }
        ?>
    </p>
    <a href="agendar_dj.php?id=<?php echo $dj['id_usuario']; ?>">Agendar</a>
</div>
        <?php endwhile; ?>
    </div>
</div>

<footer>
    © 2025 - Song Manager. Todos os direitos reservados.
</footer>

</body>
</html>
