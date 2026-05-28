<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "cliente") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_cliente = (int) $_SESSION["id"];

// Busca todas as reservas do cliente com nome do DJ
$sql = $con->prepare("
    SELECT 
        r.id_reserva,
        u.nome AS nome_dj,
        p.foto AS foto_dj,
        r.data_evento,
        r.horario_ini,
        r.horario_fim,
        r.local_evento,
        r.tipo_evento,
        r.status,
        r.data_reserva
    FROM reservas r
    JOIN usuarios u ON u.id_usuario = r.id_dj
    LEFT JOIN perfis_dj p ON p.id_usuario = r.id_dj
    WHERE r.id_cliente = ?
    ORDER BY r.data_evento DESC
");
$sql->bind_param("i", $id_cliente);
$sql->execute();
$reservas = $sql->get_result();

// Cores por status
function badge($status) {
    $map = [
        'pendente'  => ['cor' => '#856404', 'bg' => '#fff3cd', 'label' => 'Pendente'],
        'aprovado'  => ['cor' => '#155724', 'bg' => '#d4edda', 'label' => 'Aprovado'],
        'recusado'  => ['cor' => '#721c24', 'bg' => '#f8d7da', 'label' => 'Recusado'],
        'cancelado' => ['cor' => '#383d41', 'bg' => '#e2e3e5', 'label' => 'Cancelado'],
    ];
    $s = $map[$status] ?? ['cor' => '#333', 'bg' => '#eee', 'label' => $status];
    return "<span style='background:{$s['bg']};color:{$s['cor']};
                padding:4px 10px;border-radius:20px;font-size:13px;
                font-weight:bold'>{$s['label']}</span>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Minhas Reservas - Song Manager</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; color:#111; }

header { width:100%; background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:26px; }
nav { display:flex; gap:15px; flex-wrap:wrap; }
nav a { text-decoration:none; color:black; font-size:16px; padding:8px 16px; background:#28a745; border-radius:8px; border:1px solid #1e7e34; }
nav a:hover { background:#1e7e34; color:#fff; }

.container { max-width:1000px; margin:40px auto; padding:0 20px; }
.container h2 { margin-bottom:25px; }

.msg-ok   { background:#d4edda; color:#155724; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold; }
.msg-erro { background:#f8d7da; color:#721c24; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold; }

.card-reserva {
    background:#fff;
    border-radius:12px;
    box-shadow:0 0 12px #0001;
    padding:20px 25px;
    margin-bottom:20px;
    display:flex;
    gap:20px;
    align-items:center;
    flex-wrap:wrap;
}

.card-reserva img {
    width:80px; height:80px;
    border-radius:50%;
    object-fit:cover;
    flex-shrink:0;
    border:3px solid #28a745;
}

.card-reserva .sem-foto {
    width:80px; height:80px;
    border-radius:50%;
    background:#e9ecef;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; flex-shrink:0;
    border:3px solid #ccc;
}

.card-reserva .info { flex:1; }
.card-reserva .info h3 { margin-bottom:8px; font-size:18px; }
.card-reserva .info p  { margin-bottom:5px; font-size:15px; color:#444; }

.card-reserva .acoes { display:flex; flex-direction:column; gap:8px; align-items:flex-end; }

.btn-cancelar {
    padding:8px 16px;
    background:#dc3545;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
}
.btn-cancelar:hover { background:#a71d2a; }

.vazio { text-align:center; padding:60px 20px; color:#888; }
.vazio p { font-size:18px; margin-bottom:20px; }
.vazio a { text-decoration:none; background:#28a745; color:#fff; padding:12px 24px; border-radius:8px; }

footer { text-align:center; padding:30px; background:#e9ecef; margin-top:40px; }

@media(max-width:600px) {
    .card-reserva { flex-direction:column; align-items:flex-start; }
    .card-reserva .acoes { align-items:flex-start; }
}
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
        <a href="home_cliente.php">Início</a>
        <a href="meus_agendamentos.php">Minhas Reservas</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Minhas Reservas</h2>

    <?php if (isset($_SESSION['msg_ok'])): ?>
        <div class="msg-ok"><?php echo $_SESSION['msg_ok']; unset($_SESSION['msg_ok']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['msg_erro'])): ?>
        <div class="msg-erro"><?php echo $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div>
    <?php endif; ?>

    <?php if ($reservas->num_rows === 0): ?>
        <div class="vazio">
            <p>Você ainda não fez nenhuma reserva.</p>
            <a href="home_cliente.php">Encontrar DJs</a>
        </div>
    <?php else: ?>
        <?php while ($r = $reservas->fetch_assoc()): ?>
        <div class="card-reserva">

            <?php if (!empty($r['foto_dj'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($r['foto_dj']); ?>" alt="Foto DJ">
            <?php else: ?>
                <div class="sem-foto">🎧</div>
            <?php endif; ?>

            <div class="info">
                <h3><?php echo htmlspecialchars($r['nome_dj']); ?></h3>
                <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($r['data_evento'])); ?></p>
                <p><strong>Horário:</strong> <?php echo substr($r['horario_ini'], 0, 5); ?> às <?php echo substr($r['horario_fim'], 0, 5); ?></p>
                <p><strong>Local:</strong> <?php echo htmlspecialchars($r['local_evento'] ?? '-'); ?></p>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($r['tipo_evento'] ?? '-'); ?></p>
                <p><strong>Reservado em:</strong> <?php echo date('d/m/Y H:i', strtotime($r['data_reserva'])); ?></p>
            </div>

            <div class="acoes">
                <?php echo badge($r['status']); ?>

                <?php if ($r['status'] === 'pendente'): ?>
                <form method="POST" action="cancelar_reserva.php"
                      onsubmit="return confirm('Deseja cancelar esta reserva?')">
                    <input type="hidden" name="id_reserva" value="<?php echo $r['id_reserva']; ?>">
                    <button type="submit" class="btn-cancelar">Cancelar</button>
                </form>
                <?php endif; ?>
            </div>

        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer>© 2025 - Song Manager. Todos os direitos reservados.</footer>

</body>
</html>