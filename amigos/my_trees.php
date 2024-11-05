<?php
include '../includes/functions.php';
//session_start();

check_amigo();

$amigo_id = $_SESSION['usuario_id'];
$purchased_trees = get_amigo_trees($amigo_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Trees</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-success text-center">My Purchased Trees</h2>
        <div class="row">
            <?php foreach ($purchased_trees as $tree): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="img/<?= htmlspecialchars($tree['foto']) ?>" class="card-img-top" alt="Tree Image">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($tree['ubicacion_geografica']) ?></h5>
                            <p class="card-text">Purchased on: <?= htmlspecialchars($tree['fecha_compra']) ?></p>
                            <a href="tree_details.php?id=<?= $tree['id'] ?>" class="btn btn-outline-success">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
