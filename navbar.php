<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-md bg-dark" data-bs-theme="dark">
            <div class="container-xxl">
                <a href="home.php" class="navbar-brand">
                    <span class="fw-bold">
                        RINZify - Project Manager
                    </span>
                </a>

                <!--toggle for mobile-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav" 
                aria-controls="main-nav" aria-expanded="false" aria-label="toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!--navbar links-->
                <div class="collapse navbar-collapse justify-content-end align-center" id="main-nav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link <?php if($currentPage == 'home.php') echo 'active'; ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link <?php if($currentPage == 'dashboard.php') echo 'active'; ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="projects.php" class="nav-link <?php if($currentPage == 'projects.php') echo 'active'; ?>">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Log out</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>