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

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
    $project_name = trim($_POST['project_name']);
    $client_id = !empty($_POST['client_id']) ? intval($_POST['client_id']) : null;
    $location = trim($_POST['location']);
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $total_budget = !empty($_POST['total_budget']) ? floatval($_POST['total_budget']) : 0.00;

    if (!empty($project_name)) {
        $insert_stmt = $conn->prepare("INSERT INTO projects (client_id, project_name, location, start_date, end_date, total_budget, is_active) VALUES (?, ?, ?, ?, ?, ?, 0)");
        $insert_stmt->bind_param("issssd", $client_id, $project_name, $location, $start_date, $end_date, $total_budget);
        
        if ($insert_stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error_msg = "Failed to create new project profile.";
        }
    } else {
        $error_msg = "Project name is a mandatory field.";
    }
}

$clients_list = [];
$clients_query = $conn->query("SELECT client_id, name FROM clients ORDER BY name ASC");
if ($clients_query) {
    while ($c_row = $clients_query->fetch_assoc()) {
        $clients_list[] = $c_row;
    }
}

$sql = "SELECT p.*, c.name AS client_name 
        FROM projects p 
        LEFT JOIN clients c ON p.client_id = c.client_id 
        ORDER BY p.is_active DESC, p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'head.php';?>
    <style>
        #projectSearch:focus { border-color: #dfa875 !important; box-shadow: 0 0 0 0.2rem rgba(223, 168, 117, 0.25); outline: none; }
    </style>

    <body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, -apple-system, sans-serif;">
        <div class="d-flex">
            <?php include 'navbar.php';?>
            <div class="flex-grow-1 min-vh-100 d-flex flex-column">
                <main class="container-fluid px-4 pb-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                        <div>
                            <h3 class="fw-bold m-0" style="color: #3d2621; letter-spacing: -0.5px;">Projects</h3>
                            <p class="text-muted small m-0">Manage your renovation projects.</p>
                        </div>
                        <button class="btn btn-sm text-white px-3 py-2 fw-medium shadow-sm" style="background-color: #dfa875; border: none;" data-bs-toggle="modal" data-bs-target="#addNewProjectModal">
                            <i class="bi bi-plus-lg me-1"></i> New Project
                        </button>
                    </div>

                    <div class="mb-4">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute" style="top: 50%; left: 15px; transform: translateY(-50%); color: #aaa;"></i>
                            <input type="text" id="projectSearch" 
                                   class="form-control rounded-pill ps-5 py-2 shadow-sm border-0" 
                                   placeholder="Search projects by name, client, or location..." 
                                   onkeyup="filterProjects()"
                                   style="transition: all 0.3s;">
                        </div>
                    </div>

                    <div class="row g-4" id="projectGrid">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="col-12 col-md-6 col-lg-4 project-card-wrapper">
                                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden transition-all" 
                                         style="background-color: #ffffff; <?php if($row['is_active'] == 1) echo 'box-shadow: 0 0 0 2px #dfa875, 0 4px 15px rgba(223,168,117,0.15) !important;'; ?>">
                                        <div class="card-header fw-bold border-0 pt-4 pb-1 bg-white d-flex justify-content-between align-items-start">
                                            <span class="text-truncate me-2"><?= htmlspecialchars($row['project_name']) ?></span>
                                            <?php if($row['is_active'] == 1): ?>
                                                <span class="badge rounded-pill px-2.5 py-1 small" style="background-color: rgba(223, 168, 117, 0.2); color: #be7a3e;">Active</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body d-flex flex-column justify-content-between pt-2 pb-4">
                                            <div class="mb-4">
                                                <div class="d-flex align-items-center mb-3 text-muted small">
                                                    <i class="bi bi-person me-2" style="color: #dfa875;"></i>
                                                    <span>Client: <strong><?= htmlspecialchars($row['client_name'] ?? 'Unassigned') ?></strong></span>
                                                </div>
                                                <div class="d-flex align-items-start mb-3 text-muted small">
                                                    <i class="bi bi-geo-alt me-2 mt-0.5" style="color: #dfa875;"></i>
                                                    <span><?= htmlspecialchars($row['location'] ?? 'No Address') ?></span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="badge rounded-pill px-2" 
                                                      style="background-color: <?= ($row['status'] === 'completed') ? 'rgba(40, 167, 69, 0.15)' : 'rgba(23, 162, 184, 0.15)' ?>; 
                                                             color: <?= ($row['status'] === 'completed') ? '#28a745' : '#17a2b8' ?>;">
                                                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                                </span>
                                                
                                                <?php if($row['is_active'] == 0): ?>
                                                    <form action="activate_project.php" method="POST" class="m-0">
                                                        <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                                                        
                                                        <?php if($row['status'] === 'completed'): ?>
                                                            <!-- Button for Completed Projects -->
                                                            <button type="submit" class="btn btn-sm text-white px-3 rounded-2" style="background-color: #6c757d;">Preview</button>
                                                        <?php else: ?>
                                                            <!-- Button for In-Progress Projects -->
                                                            <button type="submit" class="btn btn-sm text-white px-3 rounded-2" style="background-color: #dfa875;">Work On</button>
                                                        <?php endif; ?>
                                                    </form>
                                                <?php else: ?>
                                                    <a href="dashboard.php" class="btn btn-sm px-3 rounded-2" style="background-color: #eaddd0;">View Active</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>

        <div class="modal fade" id="addNewProjectModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="modal-content border-0 shadow rounded-3">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold">Launch New Project</h5>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="create_project" value="1">
                        <input type="text" name="project_name" class="form-control mb-3" placeholder="Project Name" required>
                        <select name="client_id" class="form-select mb-3">
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clients_list as $client): ?>
                                <option value="<?= $client['client_id'] ?>"><?= htmlspecialchars($client['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea name="location" class="form-control mb-3" placeholder="Location"></textarea>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><input type="date" name="start_date" class="form-control" placeholder="Start Date"></div>
                            <div class="col-6"><input type="date" name="end_date" class="form-control" placeholder="End Date"></div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-0" style="background-color: #f0eae1;">$</span>
                            <input type="number" name="total_budget" class="form-control" placeholder="Total Budget" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3">
                        <button type="submit" class="btn text-white" style="background-color: #dfa875;">Create Project</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        function filterProjects() {
            const filter = document.getElementById("projectSearch").value.toLowerCase();
            const cards = document.getElementsByClassName("project-card-wrapper");
            for (let i = 0; i < cards.length; i++) {
                cards[i].style.display = cards[i].innerText.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
        </script>
    </body>
</html>