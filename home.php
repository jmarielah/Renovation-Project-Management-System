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
?>

<!DOCTYPE html>
<html>
     
    <?php include 'head.php';?>

    <body>

    <!--navigation bar-->
    <?php include 'navbar.php';?>

        <!--intro page-->
        <section id="home">
            <div class="container-fluid px-0 mb-5">

                <div class="row">
                    <div id="carousel" class="carousel slide carousel-fade position-relative" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-overlay"></div>
                        <div class="carousel-caption d-flex justify-content-center align-items-center h-100" style="z-index:2;">
                            <h1 class="text-white fw-bold display-3">
                                Renovation Project Management System
                            </h1>
                        </div>

                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="assets/design_example1.jpg" class="d-block w-100" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/design_example2.jpg" class="d-block w-100" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/design_example3.jpg" class="d-block w-100" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/design_example4.jpg" class="d-block w-100" alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <div class="row mt-3 px-5">
                    <h2 class="fw-bold">FEATURES</h2>
                    <ul>
                        <li class="text-secondary mt-2 px-10">
                            <h5 class="fw-bold">Progress and Expense Tracker</h5>
                            <p>This subsystem will track progress and allow users to monitor design phases, from design approval to completion. Additionally, it will allow users to log expenses to create accurate summaries.</p>
                        </li>
                        <li class="text-secondary mt-2 px-10">
                            <h5 class="fw-bold">Project Records</h5>
                            <p>This subsystem will store all projects handled by the company along with client information and the necessary documents.</p>
                        </li>
                        <li class="text-secondary mt-2 px-10">
                            <h5 class="fw-bold">Cost Planning</h5>
                            <p>This subsystem will generate budget estimates to help deliver an appropriate total project cost.</p>
                        </li>
                        <li class="text-secondary mt-2 px-10">
                            <h5 class="fw-bold">Expense Tracking</h5>
                            <p>This subsystem will record expenses.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>