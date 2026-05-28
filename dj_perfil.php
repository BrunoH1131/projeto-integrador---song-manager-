<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_usuario = (int) $_SESSION["id"];

// Busca perfil atual do DJ
$sql = $con->prepare("SELECT * FROM perfis_dj WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$perfil = $sql->get_result()->fetch_assoc();

// Busca categorias disponíveis
$cats = $con->query("SELECT * FROM categorias_dj ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Meu Perfil - Song Manager</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; color:#111; }

header { width:100%; background:#fff; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px #0002; }
header h2 { font-size:26px; }
nav a { text-decoration:none; color:black; font-size:16px; padding:8px 16px; background:#28a745; border-radius:8px; border:1px solid #1e7e34; margin-left:10px; }
nav a:hover { background:#1e7e34; color:#fff; }

.container { max-width:700px; margin:40px auto; padding:0 20px; }

.card {
    background:#fff;
    border-radius:12px;
    box-shadow:0 0 12px #0001;
    padding:30px;
}

.card h2 { margin-bottom:25px; font-size:22px; }

.foto-atual {
    display:flex;
    align-items:center;
    gap:20px;
    margin-bottom:25px;
}

.foto-atual img {
    width:100px;
    height:100px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #28a745;
}

.foto-atual .sem-foto {
    width:100px;
    height:100px;
    border-radius:50%;
    background:#e9ecef;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:40px;
    border:3px solid #ccc;
}

label { display:block; margin-bottom:6px; font-weight:bold; font-size:15px; }

input[type=text],
input[type=number],
input[type=file],
select,
textarea {
    width:100%;
    padding:12px;
    margin-bottom:20px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
    font-family:Arial;
}

textarea { resize:vertical; min-height:100px; }

button {
    width:100%;
    padding:14px;
    background:#28a745;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}
button:hover { background:#1e7e34; }

.msg-ok { background:#d4edda; color:#155724; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold; }

footer { text-align:center; padding:30px; background:#e9ecef; margin-top:40px; }
</style>
</head>
<body>

<header>
    <h2>Song Manager</h2>
    <nav>
        <a href="home_dj.php">Início</a>
        <a href="agendamentos_dj.php">Agendamentos</a>
        <?php include "header.php"; ?>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">

    <?php if (isset($_SESSION['msg_ok'])): ?>
        <div class="msg-ok"><?php echo $_SESSION['msg_ok']; unset($_SESSION['msg_ok']); ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Meu Perfil</h2>

        <!-- Foto atual -->
        <div class="foto-atual">
            <?php if (!empty($perfil['foto'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($perfil['foto']); ?>" alt="Foto">
            <?php else: ?>
                <div class="sem-foto">🎧</div>
            <?php endif; ?>
            <div>
                <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong><br>
                <span style="color:#888;font-size:14px">DJ</span>
            </div>
        </div>

        <form action="dj_perfil_processa.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

            <label>Estilo Musical</label>
            <select name="id_categoria" required>
                <option value="">Selecione...</option>
                <?php while ($cat = $cats->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>"
                        <?php echo ($perfil['id_categoria'] ?? '') == $cat['id_categoria'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nome']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Preço por evento (R$)</label>
            <input type="number" step="0.01" name="preco" placeholder="Ex: 500.00"
                   value="<?php echo $perfil['preco'] ?? ''; ?>" required>

            <label>Descrição profissional</label>
            <textarea name="bio" placeholder="Conte um pouco sobre você e sua experiência..."><?php
                echo htmlspecialchars($perfil['bio'] ?? '');
            ?></textarea>

            <label>Foto de perfil</label>
            <input type="file" name="foto" accept="image/*">
            <?php if (!empty($perfil['foto'])): ?>
                <p style="font-size:13px;color:#888;margin-top:-15px;margin-bottom:20px">
                    Deixe em branco para manter a foto atual.
                </p>
            <?php endif; ?>

            <button type="submit">Salvar Perfil</button>
        </form>
    </div>
</div>

<footer>© 2025 - Song Manager. Todos os direitos reservados.</footer>

</body>
</html>