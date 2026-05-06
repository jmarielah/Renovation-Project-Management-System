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

        <h3 class="mx-3 mt-5 fw-bold">Project Title : Location</h3>

        <div class="container-lg d-flex justify-content-start">
        <div class="card row col-sm-8 col-md-4 my-3 mx-3" id="progress-card">
            <div class="card-header fw-bold bg-dark text-light">
                Phases
            </div>

            <ul class="list-group list-group-flush">

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Documents</span>
                    <div class="progress w-50" style="height:6px;">
                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                    </div>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Cost Estimation</span>
                    <div class="progress w-50" style="height:6px;">
                        <div class="progress-bar bg-warning" style="width: 60%;"></div>
                    </div>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Resource Acquisition</span>
                    <div class="progress w-50" style="height:6px;">
                        <div class="progress-bar" style="width: 30%;"></div>
                    </div>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Construction</span>
                    <div class="progress w-50" style="height:6px;">
                        <div class="progress-bar" style="width: 30%;"></div>
                    </div>
                </li>

            </ul>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>