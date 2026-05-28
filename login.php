<?php
session_start();
require "conexao.php";

$mensagem = "";

// Mensagem de cadastro
if (isset($_GET["cadastro"]) && $_GET["cadastro"] == "ok") {
    $mensagem = "<p class='sucesso'>Cadastro realizado com sucesso! Faça login.</p>";
}

// Mensagem de erro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = $con->prepare("SELECT id_usuario, nome, senha, tipo FROM usuarios WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $sql->store_result();

    if ($sql->num_rows > 0) {
        $sql->bind_result($id, $nome, $hash, $tipo);
        $sql->fetch();

        if (password_verify($senha, $hash)) {
            $_SESSION["id"] = $id;
            $_SESSION["nome"] = $nome;
            $_SESSION["tipo"] = $tipo;

            if ($tipo == "admin") header("Location: home_admin.php");
            if ($tipo == "dj") header("Location: home_dj.php");
            if ($tipo == "cliente") header("Location: home_cliente.php");
            exit;
        }
    }

    $mensagem = "<p class='erro'>Email ou senha incorretos!</p>";
}
?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <h2>Login</h2>

    <?= $mensagem ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>

        <p style="text-align:center; margin-top:10px;">
            Não tem conta? <a href="cadastro.php">Cadastre-se</a>
        </p>
    </form>
</div>
