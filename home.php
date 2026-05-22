<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
     
    <?php include 'head.php';?>

    <body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, -apple-system, sans-serif;">

        <header class="navbar navbar-expand bg-white px-4 py-3 shadow-sm d-flex justify-content-between align-items-center">
            <a href="home.php" class="text-decoration-none fw-bold fs-5 m-0" style="color: #3d2621; letter-spacing: 0.5px;">
                RINZify
            </a>
            
            <div>
                <?php if (isset($_SESSION['userID'])): ?>
                    <a href="dashboard.php" class="btn btn-sm text-white fw-medium px-4 py-2 rounded-2 shadow-sm" style="background-color: #dfa875; border: none;">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm fw-medium px-4 py-2 text-dark bg-white border border-secondary-subtle rounded-2 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Log in
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <section id="home">
            <div class="container-fluid px-0 mb-5">

                <div class="row g-0 position-relative mb-5" style="max-height: 550px; overflow: hidden;">
                    <div id="carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                        
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(61, 38, 33, 0.45); z-index: 1;"></div>
                        
                        <div class="carousel-caption d-flex justify-content-center align-items-center h-100 start-0 end-0 bottom-0" style="z-index: 2;">
                            <div class="px-3 text-center">
                                <h1 class="text-white fw-bold display-4 mb-2" style="letter-spacing: -1px; text-shadow: 0 2px 10px rgba(0,0,0,0.15);">
                                    Renovation Project Management System
                                </h1>
                                <p class="text-white-50 fs-5 fw-medium">Track and manage everything during renovation.</p>
                            </div>
                        </div>

                        <div class="carousel-inner">
                            <div class="carousel-item active" style="height: 500px;">
                                <img src="assets/design_example1.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Interior Design Concept">
                            </div>
                            <div class="carousel-item" style="height: 500px;">
                                <img src="assets/design_example2.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Workspace Setup">
                            </div>
                            <div class="carousel-item" style="height: 500px;">
                                <img src="assets/design_example3.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Renovation Concept">
                            </div>
                            <div class="carousel-item" style="height: 500px;">
                                <img src="assets/design_example4.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Architectural Plan">
                            </div>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev" style="z-index: 3;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next" style="z-index: 3;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <div class="container px-4 px-md-5 mt-4">
                    <div class="text-center mb-5">
                        <small class="text-uppercase fw-bold pb-2 d-inline-block border-bottom border-2" style="color: #dfa875; letter-spacing: 1.5px; font-size: 0.8rem;">Core Subsystems</small>
                        <h2 class="fw-bold mt-2" style="color: #3d2621; letter-spacing: -0.5px;">SYSTEM FEATURES</h2>
                    </div>

                    <div class="row g-4">
                        
                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background-color: #ffffff;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                        <span class="fw-bold" style="color: #dfa875;">01</span>
                                    </div>
                                    <h5 class="fw-bold m-0" style="color: #3d2621;">Progress and Expense Tracker</h5>
                                </div>
                                <p class="text-muted small mb-0 lh-base">This subsystem will track progress and allow users to monitor design phases, from design approval to completion. Additionally, it will allow users to log expenses to create accurate summaries.</p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background-color: #ffffff;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                        <span class="fw-bold" style="color: #dfa875;">02</span>
                                    </div>
                                    <h5 class="fw-bold m-0" style="color: #3d2621;">Project Records</h5>
                                </div>
                                <p class="text-muted small mb-0 lh-base">This subsystem will store all projects handled by the company along with client information and the necessary legal agreements or documents.</p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background-color: #ffffff;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                        <span class="fw-bold" style="color: #dfa875;">03</span>
                                    </div>
                                    <h5 class="fw-bold m-0" style="color: #3d2621;">Cost Planning</h5>
                                </div>
                                <p class="text-muted small mb-0 lh-base">This subsystem will generate real-time structural budget estimates to help layout delivery forecasts and generate appropriate total project financial paths.</p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-0 shadow-sm rounded-3 p-4" style="background-color: #ffffff;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                        <span class="fw-bold" style="color: #dfa875;">04</span>
                                    </div>
                                    <h5 class="fw-bold m-0" style="color: #3d2621;">Expense Tracking</h5>
                                </div>
                                <p class="text-muted small mb-0 lh-base">This subsystem will log specific project outlays, sync material pricing with receipts, and record day-to-day operations costs safely.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <script>
            window.addEventListener("pageshow", function (event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
         </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>