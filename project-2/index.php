<?php
require_once 'includes/header.php';
checkLogin();

// Get statistics based on user role
$stats = [];

if ($_SESSION['role'] === 'admin') {
    // Get total counts
    $stmt = $pdo->query("SELECT 
        (SELECT COUNT(*) FROM Project) as total_projects,
        (SELECT COUNT(*) FROM Student) as total_students,
        (SELECT COUNT(*) FROM Encadrant) as total_encadrants,
        (SELECT COUNT(*) FROM Reservation WHERE is_approved = 1) as approved_reservations");
    $stats = $stmt->fetch();
} elseif ($_SESSION['role'] === 'encadrant') {
    // Get encadrant's projects stats
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_projects,
        SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_projects,
        (SELECT COUNT(*) FROM Reservation r 
         JOIN Project p ON r.project_id = p.project_id 
         WHERE p.encadrant_matricule = ?) as total_reservations
        FROM Project 
        WHERE encadrant_matricule = ?");
    $stmt->execute([$_SESSION['user_matricule'], $_SESSION['user_matricule']]);
    $stats = $stmt->fetch();
} elseif ($_SESSION['role'] === 'eleve') {
    // Get student's reservations
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_reservations,
        SUM(CASE WHEN is_approved = 1 THEN 1 ELSE 0 END) as approved_reservations,
        SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending_reservations
        FROM Reservation 
        WHERE student1_matricule = ? OR student2_matricule = ?");
    $stmt->execute([$_SESSION['user_matricule'], $_SESSION['user_matricule']]);
    $stats = $stmt->fetch();
}
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Tableau de bord</h1>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Projets</h5>
                            <p class="card-text display-4"><?php echo $stats['total_projects']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Étudiants</h5>
                            <p class="card-text display-4"><?php echo $stats['total_students']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Encadrants</h5>
                            <p class="card-text display-4"><?php echo $stats['total_encadrants']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Réservations Approuvées</h5>
                            <p class="card-text display-4"><?php echo $stats['approved_reservations']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($_SESSION['role'] === 'encadrant'): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Projets</h5>
                            <p class="card-text display-4"><?php echo $stats['total_projects']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Projets Disponibles</h5>
                            <p class="card-text display-4"><?php echo $stats['available_projects']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Réservations</h5>
                            <p class="card-text display-4"><?php echo $stats['total_reservations']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($_SESSION['role'] === 'eleve'): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Réservations</h5>
                            <p class="card-text display-4"><?php echo $stats['total_reservations']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Réservations Approuvées</h5>
                            <p class="card-text display-4"><?php echo $stats['approved_reservations']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Réservations en Attente</h5>
                            <p class="card-text display-4"><?php echo $stats['pending_reservations']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actions Rapides</h5>
                        <div class="list-group">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="admin/users.php" class="list-group-item list-group-item-action">
                                    Gérer les Utilisateurs
                                </a>
                                <a href="admin/projects.php" class="list-group-item list-group-item-action">
                                    Gérer les Projets
                                </a>
                            <?php elseif ($_SESSION['role'] === 'encadrant'): ?>
                                <a href="encadrant/create-project.php" class="list-group-item list-group-item-action">
                                    Créer un Nouveau Projet
                                </a>
                                <a href="encadrant/my-projects.php" class="list-group-item list-group-item-action">
                                    Gérer mes Projets
                                </a>
                            <?php elseif ($_SESSION['role'] === 'eleve'): ?>
                                <a href="projects.php" class="list-group-item list-group-item-action">
                                    Voir les Projets Disponibles
                                </a>
                                <a href="student/my-reservations.php" class="list-group-item list-group-item-action">
                                    Mes Réservations
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 