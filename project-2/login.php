<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM Unit WHERE unit_name = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['unit_id'];
            $_SESSION['role'] = $user['role'];
            
            // Get user details based on role
            switch ($user['role']) {
                case 'admin':
                    $stmt = $pdo->prepare("SELECT * FROM Admin WHERE unit_id = ?");
                    break;
                case 'encadrant':
                    $stmt = $pdo->prepare("SELECT * FROM Encadrant WHERE unit_id = ?");
                    break;
                case 'eleve':
                    $stmt = $pdo->prepare("SELECT * FROM Student WHERE unit_id = ?");
                    break;
            }
            
            $stmt->execute([$user['unit_id']]);
            $userDetails = $stmt->fetch();
            
            $_SESSION['user_name'] = $userDetails['nom'] . ' ' . $userDetails['prenom'];
            $_SESSION['user_matricule'] = $userDetails['matricule'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = 'Identifiants invalides';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Military Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Connexion</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo sanitize($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 