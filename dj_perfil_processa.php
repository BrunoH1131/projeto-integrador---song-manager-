<?php
session_start();

if (!isset($_SESSION["tipo"]) || $_SESSION["tipo"] !== "dj") {
    header("Location: login.php");
    exit;
}

require "conexao.php";

$id_usuario  = (int) $_POST["id_usuario"];
$id_categoria = (int) $_POST["id_categoria"];
$preco       = (float) $_POST["preco"];
$bio         = trim($_POST["bio"]);
$foto        = null;

// Validação básica
if ($preco <= 0) {
    $_SESSION['msg_erro'] = "Preço inválido.";
    header("Location: dj_perfil.php");
    exit;
}

// Upload da foto
if (!empty($_FILES["foto"]["name"])) {
    $ext      = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
    $permitidos = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($ext, $permitidos)) {
        $_SESSION['msg_erro'] = "Formato de foto inválido. Use JPG, PNG ou WEBP.";
        header("Location: dj_perfil.php");
        exit;
    }

    $foto = time() . "_" . $id_usuario . "." . $ext;
    move_uploaded_file($_FILES["foto"]["tmp_name"], "uploads/" . $foto);
}

// Verifica se perfil já existe
$sql = $con->prepare("SELECT id_dj, foto FROM perfis_dj WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$perfil = $sql->get_result()->fetch_assoc();

if ($perfil) {
    // Mantém foto antiga se não enviou nova
    if (!$foto) $foto = $perfil["foto"];

    $update = $con->prepare("
        UPDATE perfis_dj 
        SET id_categoria = ?, preco = ?, bio = ?, foto = ?
        WHERE id_usuario = ?
    ");
    $update->bind_param("idssi", $id_categoria, $preco, $bio, $foto, $id_usuario);
    $update->execute();

} else {
    $insert = $con->prepare("
        INSERT INTO perfis_dj (id_usuario, id_categoria, preco, bio, foto)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iidss", $id_usuario, $id_categoria, $preco, $bio, $foto);
    $insert->execute();
}

$_SESSION['msg_ok'] = "Perfil atualizado com sucesso!";
header("Location: dj_perfil.php");
exit;
?>