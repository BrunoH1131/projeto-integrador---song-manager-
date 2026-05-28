<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Song Manager</title>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        width: 100%;
        overflow-x: hidden; /* FORÇA remover scroll lateral */
        font-family: Arial, Helvetica, sans-serif;
        background: #f5f5f5;
        color: #111;
    }

    /* MENU SUPERIOR */
    header {
        width: 100%;
        background: #ffffff;
        padding: 15px 5%;   /* diminui paddings */
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px #0002;
    }

    header h2 {
        font-size: 26px;
        white-space: nowrap; /* impede quebrar errado */
    }

    nav {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    nav a {
        text-decoration: none;
        color: black;
        font-size: 18px;
        padding: 8px 16px;
        background: #28a745;
        border-radius: 8px;
        border: 1px solid #1e7e34;
        white-space: nowrap;
    }

    /* HERO SECTION */
    .hero {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 5%;
        max-width: 1200px;
        margin: auto;
        gap: 40px;
        flex-wrap: wrap;
    }

    .hero img {
        width: 100%;
        max-width: 520px;
        height: auto;
        border-radius: 14px;
        box-shadow: 0 0 12px #0003;
    }

    .hero-text {
        max-width: 480px;
    }

    h1 {
        font-size: 38px;
        margin-bottom: 15px;
    }

    p {
        font-size: 19px;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    footer {
        text-align: center;
        padding: 30px;
        background: #e9ecef;
        color: #111;
        margin-top: 40px;
    }

    /* RESPONSIVO REAL */
    @media (max-width: 700px) {
        header {
            flex-direction: column;
            gap: 15px;
        }

        nav {
            justify-content: center;
        }

        h1 {
            font-size: 32px;
        }
    }
</style>
</head>

<body>

<header>
    <h2>Song Manager</h2>

    <nav>
        <a href="login.php">Entrar</a>
        <a href="cadastro.php">Cadastre-se</a>
        <a href="#">Fale Conosco</a>
    </nav>
</header>

<div class="hero">
    <img src="https://images.unsplash.com/photo-1507874457470-272b3c8d8ee2?auto=format&fit=crop&w=1200&q=80">

    <div class="hero-text">
        <h1>Encontre o DJ perfeito para sua festa</h1>
        <p>Gerencie eventos, encontre DJs disponíveis e facilite tudo com poucos cliques.
           Uma solução moderna para quem quer organizar eventos com praticidade.</p>
    </div>
</div>

<footer>
    © 2025 - Song Manager. Todos os direitos reservados.
</footer>

</body>
</html>
