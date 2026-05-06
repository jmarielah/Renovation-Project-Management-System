<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
    <?php include 'head.php';?>

    <body>
        <?php include 'navbar.php';?>

        <?php
        $sql = "SELECT * FROM projects";
        $result = $conn->query($sql);
        ?>

        <div class="container mt-4">

    <h3 class="mb-4 fw-bold">Projects</h3>

    <div class="row">

        <?php while($row = $result->fetch_assoc()): ?>

            <div class="col-md-4 mb-3">

                <div class="card shadow-sm">

                    <div class="card-header fw-bold bg-dark text-light">
                        <?= $row['project_name'] ?>
                    </div>

                    <div class="card-body">

                        <p><strong>Start:</strong> <?= $row['start_date'] ?></p>
                        <p><strong>End:</strong> <?= $row['end_date'] ?></p>

                        <!-- Status badge -->
                        <?php if($row['status'] == 'Completed'): ?>
                            <span class="badge bg-success">Completed</span>

                        <?php elseif($row['status'] == 'In Progress'): ?>
                            <span class="badge bg-warning">In Progress</span>

                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <?= $row['status'] ?>
                            </span>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    </div>
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>