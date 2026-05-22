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

$active_project_query = $conn->query("SELECT * FROM projects WHERE is_active = 1 LIMIT 1");
$current_project = $active_project_query->fetch_assoc();

$project_id = $current_project ? $current_project['project_id'] : null;
$project_title = $current_project ? $current_project['project_name'] : "No Active Project";
$project_location = $current_project ? $current_project['location'] : "Select a project from the panel";
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'head.php';?>

    <body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, -apple-system, sans-serif;">
        
        <div class="d-flex">
            
            <?php include 'navbar.php';?>

            <div class="flex-grow-1 min-vh-100 d-flex flex-column">

                <main class="container-fluid px-4 pb-5">

                    <div class="row mt-4 mb-3">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3 bg-white p-2 d-flex flex-row justify-content-between align-items-center">
                                <span class="fs-6 fw-semibold ps-2" style="color: #3d2621;">
                                    <i class="bi bi-person-circle me-1" style="color: #dfa875;"></i> 
                                    Welcome, <?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User' ?>!
                                </span>
                                
                                <a href="logout.php" class="btn btn-sm fw-medium px-4 py-2 text-danger-emphasis bg-light border border-danger-subtle rounded-2 shadow-sm">
                                    <i class="bi bi-box-arrow-right me-1"></i> Log out
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <div>
                            <h3 class="fw-bold m-0" style="color: #3d2621; letter-spacing: -0.5px;">
                                <?= htmlspecialchars($project_title) ?> : <span class="fw-medium text-muted fs-5"><?= htmlspecialchars($project_location) ?></span>
                            </h3>
                        </div>
                        
                        <?php if ($current_project): ?>
                            <div>
                                <?php if ($current_project['status'] !== 'completed'): ?>
                                    <!-- Finish Project Button -->
                                    <form action="finish_project.php" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to mark this pipeline as completed?');">
                                        <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                        <button type="submit" class="btn btn-sm text-white px-3 py-2 fw-medium shadow-sm" style="background-color: #28a745; border: none;">
                                            <i class="bi bi-check2-circle me-1"></i> Finish Project
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <!-- Exit Preview Button -->
                                    <form action="deactivate_project.php" method="POST" class="m-0">
                                        <input type="hidden" name="project_id" value="<?= $project_id ?>">
                                        <button type="submit" class="btn btn-sm text-white px-3 py-2 fw-medium shadow-sm" style="background-color: #6c757d; border: none;">
                                            <i class="bi bi-box-arrow-left me-1"></i> Exit Preview
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!$current_project): ?>
                        <div class="alert bg-white border-0 shadow-sm p-4 rounded-3 text-center my-4">
                            <h5 class="fw-bold" style="color: #3d2621;">No Project Selected</h5>
                            <p class="text-muted small mb-3">You must set a project as active to track its milestones, expenses, and task loops.</p>
                            <a href="projects.php" class="btn btn-sm text-white px-4 py-2" style="background-color: #dfa875; border: none;">Go to Projects</a>
                        </div>
                    <?php else: ?>

                        <?php
                        $phase_stats = [];
                        $has_any_tasks = false;

                        if ($project_id) {
                            $progress_query = $conn->prepare("
                                SELECT 
                                    CASE 
                                        WHEN status IN ('Planning', 'Design', 'Documents') THEN 'Documents'
                                        WHEN status = 'Cost Estimation' THEN 'Cost Estimation'
                                        WHEN status = 'Procurement' THEN 'Resource Acquisition'
                                        WHEN status IN ('Renovation', 'Completed', 'Construction') THEN 'Construction'
                                        ELSE status 
                                    END AS calculated_phase,
                                    COUNT(*) as total_tasks,
                                    SUM(CASE WHEN is_done = 1 THEN 1 ELSE 0 END) as completed_tasks
                                FROM project_updates 
                                WHERE project_id = ? 
                                GROUP BY calculated_phase
                            ");
                            $progress_query->bind_param("i", $project_id);
                            $progress_query->execute();
                            $progress_result = $progress_query->get_result();

                            while ($row = $progress_result->fetch_assoc()) {
                                $has_any_tasks = true;
                                $total = $row['total_tasks'];
                                $completed = $row['completed_tasks'];
                                $percent = ($total > 0) ? round(($completed / $total) * 100) : 0;
                                
                                $phase_stats[$row['calculated_phase']] = [
                                    'total' => $total,
                                    'completed' => $completed,
                                    'percentage' => $percent
                                ];
                            }
                        }
                        ?>

                        <div class="row mb-2">
                            
                            <div class="col-12 col-lg-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden bg-white">
                                    <div class="card-header fw-bold border-0 py-3 bg-white" style="color: #3d2621;">Phases</div>
                                    <hr class="text-muted mx-3">
                                    
                                    <?php if (!$has_any_tasks): ?>
                                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-4">
                                            <i class="bi bi-clipboard-x display-6 text-muted mb-2 opacity-50"></i>
                                            <p class="text-muted small m-0 italic">No tasks listed yet.</p>
                                        </div>
                                    <?php else: ?>
                                        <ul class="list-group list-group-flush border-0">
                                            <?php foreach ($phase_stats as $phase_name => $data): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                                    <div>
                                                        <span class="small fw-semibold d-block text-dark-emphasis"><?= htmlspecialchars($phase_name) ?></span>
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            <?= $data['completed'] ?> of <?= $data['total'] ?> cleared
                                                        </small>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 w-50 justify-content-end">
                                                        <div class="progress w-75" style="height:6px; background-color: #f0eae1;">
                                                            <div class="progress-bar rounded-pill" style="width: <?= $data['percentage'] ?>%; background-color: #dfa875; transition: width 0.4s ease;"></div>
                                                        </div>
                                                        <span class="fw-bold small" style="color: #3d2621; font-size: 0.75rem; min-width: 30px; text-align: right;">
                                                            <?= $data['percentage'] ?>%
                                                        </span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php
                                if ($project_id) {
                                    $budget_query = $conn->prepare("SELECT total_budget FROM projects WHERE project_id = ?");
                                    $budget_query->bind_param("i", $project_id);
                                    $budget_query->execute();
                                    $budget_res = $budget_query->get_result()->fetch_assoc();
                                    $total_budget = $budget_res['total_budget'] ?? 0;

                                    $expense_query = $conn->prepare("SELECT SUM(amount) as total_spent FROM expenses WHERE project_id = ?");
                                    $expense_query->bind_param("i", $project_id);
                                    $expense_query->execute();
                                    $expense_res = $expense_query->get_result()->fetch_assoc();
                                    $total_spent = $expense_res['total_spent'] ?? 0;

                                    $percent_spent = ($total_budget > 0) ? min(round(($total_spent / $total_budget) * 100), 100) : 0;
                                }
                                ?>

                            <div class="col-12 col-lg-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                                    <div class="card-header fw-bold border-0 py-3 bg-white" style="color: #3d2621;">Financial Status</div>
                                    <div class="card-body d-flex flex-column justify-content-between py-3">
                                        <div class="mb-3">
                                            <small class="text-muted d-block text-uppercase fw-bold mb-1" style="letter-spacing: 0.5px; font-size: 0.75rem;">Budget Usage</small>
                                            <h3 class="fw-bold mb-0" style="color: #3d2621;">
                                                $<?= number_format($total_spent, 2) ?> <span class="text-muted fs-6 fw-normal">/ $<?= number_format($total_budget, 2) ?></span>
                                            </h3>
                                        </div>
                                        <div>
                                            <div class="progress rounded-pill mb-2" style="height:12px; background-color: #f0eae1;">
                                                <div class="progress-bar fw-semibold" role="progressbar" 
                                                    style="width: <?= $percent_spent ?>%; background-color: #dfa875; transition: width 0.6s ease;" 
                                                    aria-valuenow="<?= $percent_spent ?>" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted fw-bold"><?= $percent_spent ?>% of Total Budget Spent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <h4 class="fw-bold mb-3 mt-2" style="color: #3d2621; letter-spacing: -0.3px;">Directories</h4>
                        <div class="row g-4 mb-5">
                            <div class="col-12 col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white transition-all hover-lift">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                            <i class="bi bi-list-check fs-4" style="color: #dfa875;"></i>
                                        </div>
                                        <h5 class="fw-bold m-0" style="color: #3d2621;">Task Checklist</h5>
                                    </div>
                                    <ul class="text-secondary">
                                            <li>Create tasks</li>
                                            <li>Manage a project checklist</li>
                                            <li>Plan project</li>
                                    </ul>
                                    <a href="tasks.php?project_id=<?= $project_id ?>" class="btn btn-sm w-100 fw-medium rounded-2 py-2 mt-auto" style="background-color: #eaddd0; color: #5c4d4a; border: none;">
                                        Open Task Checklist <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white transition-all hover-lift">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                            <i class="bi bi-receipt-cutoff fs-4" style="color: #dfa875;"></i>
                                        </div>
                                        <h5 class="fw-bold m-0" style="color: #3d2621;">Expense Log</h5>
                                    </div>
                                    <ul class="text-secondary">
                                            <li>Record expenses</li>
                                    </ul>
                                    <a href="expenses.php?project_id=<?= $project_id ?>" class="btn btn-sm w-100 fw-medium rounded-2 py-2 mt-auto" style="background-color: #eaddd0; color: #5c4d4a; border: none;">
                                        Open Expense Log <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white transition-all hover-lift">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #fdfaf6; width: 45px; height: 45px;">
                                            <i class="bi bi-chat-dots fs-4" style="color: #dfa875;"></i>
                                        </div>
                                        <h5 class="fw-bold m-0" style="color: #3d2621;">Project Logs</h5>
                                    </div>
                                    <ul class="text-secondary">
                                            <li>Create comments</li>
                                            <li>Communicate updates</li>
                                    </ul>
                                    <a href="updates.php?project_id=<?= $project_id ?>" class="btn btn-sm w-100 fw-medium rounded-2 py-2 mt-auto" style="background-color: #eaddd0; color: #5c4d4a; border: none;">
                                        Open Project Log <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>

                </main>
            </div>
        </div>

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