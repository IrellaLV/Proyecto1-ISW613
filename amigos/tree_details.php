<?php
include 'functions.php';
session_start();

check_amigo();

if (isset($_GET['id'])) {
    $tree_id = intval($_GET['id']);
    $tree = get_tree_by_id($tree_id);
    $amigo_id = $_SESSION['usuario_id'];

    // Confirm that the tree belongs to this user
    if ($tree['amigo_id'] != $amigo_id) {
        header('Location: no_access.php');
        exit();
    }
} else {
    echo "Tree not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tree Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-success text-center">Tree Details</h2>
        <div class="card">
            <img src="img/<?= htmlspecialchars($tree['foto']) ?>" class="card-img-top" alt="Tree Image">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($tree['ubicacion_geografica']) ?></h5>
                <p class="card-text">Status: <?= htmlspecialchars($tree['estado']) ?></p>
                <p class="card-text">Price: $<?= htmlspecialchars($tree['precio']) ?></p>
                <p class="card-text">Species ID: <?= htmlspecialchars($tree['especie_id']) ?></p>
            </div>
        </div>
    </div>
</body>
</html>
