<?php
require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_BCRYPT);
    $tipo = $_POST["tipo"];

    $sql = $con->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss", $nome, $email, $senha, $tipo);

    if ($sql->execute()) {
        header("Location: login.php?cadastro=ok");
        exit;
    } else {
        header("Location: login.php?erro=cadastro");
        exit;
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Cadastrar Usuário</h2>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>

        <select name="tipo" required>
            <option value="cliente">Cliente</option>
            <option value="dj">DJ</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Cadastrar</button>
    </form>
</div>
