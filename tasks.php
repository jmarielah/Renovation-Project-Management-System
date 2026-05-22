<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit(); }
include 'db_connection.php';

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
$verify_query = $conn->query("SELECT * FROM projects WHERE project_id = $project_id AND is_active = 1 LIMIT 1");
if (!$verify_query || $verify_query->num_rows === 0) { header("Location: dashboard.php"); exit(); }
$current_project = $verify_query->fetch_assoc();

$error_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $update_note = trim($_POST['update_note']);
    $status = trim($_POST['status']);

    if (!empty($update_note) && !empty($status)) {
        $stmt = $conn->prepare("INSERT INTO project_updates (project_id, update_note, status, is_done) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("iss", $project_id, $update_note, $status);
        if ($stmt->execute()) {
            header("Location: tasks.php?project_id=" . $project_id);
            exit();
        } else { $error_msg = "Failed to log task."; }
    }
}

// Group standard checklist rows
$phases = ['Documents', 'Cost Estimation', 'Resource Acquisition', 'Construction'];
$task_groups = [];
foreach ($phases as $p) { $task_groups[$p] = []; }

$tasks_query = $conn->prepare("SELECT * FROM project_updates WHERE project_id = ? ORDER BY update_id DESC");
$tasks_query->bind_param("i", $project_id);
$tasks_query->execute();
$tasks_result = $tasks_query->get_result();

while ($row = $tasks_result->fetch_assoc()) {
    $phase_key = $row['status'];
    if ($phase_key === 'Planning' || $phase_key === 'Design') $phase_key = 'Documents';
    if ($phase_key === 'Procurement') $phase_key = 'Resource Acquisition';
    if ($phase_key === 'Renovation' || $phase_key === 'Completed') $phase_key = 'Construction';
    if (array_key_exists($phase_key, $task_groups)) {
        $task_groups[$phase_key][] = $row;
    }
}

// Fetch all calculated budget parameters for the dynamic View Modal mapping
$costs_ledger_query = $conn->prepare("SELECT * FROM comprehensive_costs WHERE project_id = ? ORDER BY id DESC");
$costs_ledger_query->bind_param("i", $project_id);
$costs_ledger_query->execute();
$costs_ledger_result = $costs_ledger_query->get_result();

$all_costs = [];
$total_cost_projected = 0;
while($c_row = $costs_ledger_result->fetch_assoc()) {
    $total_cost_projected += $c_row['calculated_total'];
    $all_costs[] = $c_row;
}
$has_costs = count($all_costs) > 0;
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'head.php';?>
    <body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, sans-serif;">
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
                    
                    <div class="mb-4">
                        <a href="dashboard.php" class="text-decoration-none small fw-medium" style="color: #dfa875;"><i class="bi bi-arrow-left me-1"></i> Back to Dashboard</a>
                        <h3 class="fw-bold mt-2" style="color: #3d2621;">Task Checklist</h3>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-top" style="top: 24px;">
                                <h5 class="fw-bold mb-3" style="color: #3d2621;">Create Task</h5>
                                <form action="tasks.php?project_id=<?= $project_id ?>" method="POST">
                                    <input type="hidden" name="add_task" value="1">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Task Description</label>
                                        <textarea name="update_note" rows="3" class="form-control form-control-sm rounded-2" placeholder="Describe task requirement..." required></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label small fw-semibold">Category of Task</label>
                                        <select name="status" class="form-select form-select-sm rounded-2" required>
                                            <option value="Documents">Documents</option>
                                            <option value="Resource Acquisition">Resource Acquisition</option>
                                            <option value="Construction">Construction</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-sm w-100 text-white fw-medium py-2 rounded-2" style="background-color: #dfa875; border: none;"><i class="bi bi-plus-lg me-1"></i> Add to Checklist</button>
                                </form>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                                <h5 class="fw-bold mb-4" style="color: #3d2621;">Checklist</h5>

                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom border-light-subtle">
                                        <h6 class="fw-bold m-0" style="color: #be7a3e;">Cost Estimation</h6>
                                    </div>
                                    <div class="py-2">
                                        <?php if (!$has_costs): ?>
                                            <a href="plan_costs.php?project_id=<?= $project_id ?>" class="btn btn-sm text-white px-4 py-2 rounded-2 fw-medium shadow-sm" style="background-color: #dfa875; border: none;">
                                                <i class="bi bi-calculator me-1"></i> Plan Costs
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-4 py-2 rounded-2 fw-medium shadow-sm bg-white" data-bs-toggle="modal" data-bs-target="#viewEstimateModal">
                                                <i class="bi bi-eye me-1"></i> View Cost Estimate
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php foreach ($task_groups as $phase_name => $tasks): ?>
                                    <?php if ($phase_name === 'Cost Estimation') continue; ?>

                                    <div class="mb-4">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom border-light-subtle">
                                            <h6 class="fw-bold m-0" style="color: #be7a3e lanes-header;"><?= $phase_name ?></h6>
                                        </div>

                                        <?php if (empty($tasks)): ?>
                                            <p class="text-muted small ps-2 italic mb-3">No tasks added to this category.</p>
                                        <?php else: ?>
                                            <div class="list-group gap-2 mb-3">
                                                <?php foreach ($tasks as $task): ?>
                                                    <label class="list-group-item d-flex gap-3 border rounded-3 py-2.5 align-items-center transition-all <?= $task['is_done'] ? 'bg-light text-muted' : '' ?>" style="cursor: pointer;">
                                                        <input class="form-check-input task-toggle-checkbox flex-shrink-0" type="checkbox" data-id="<?= $task['update_id'] ?>" <?= $task['is_done'] ? 'checked' : '' ?> style="border-color: #dfa875; cursor: pointer;">
                                                        <span class="small <?= $task['is_done'] ? 'text-decoration-line-through' : 'fw-medium' ?>"><?= htmlspecialchars($task['update_note']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <div class="modal fade" id="viewEstimateModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    
                    <div class="modal-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold m-0" style="color: #3d2621;">
                            <i class="bi bi-journal-text me-2" style="color: #dfa875;"></i>Financial Ledger
                        </h5>
                        <a href="plan_costs.php?project_id=<?= $project_id ?>" class="btn btn-sm btn-dark rounded-pill px-3 fw-medium" style="font-size: 0.75rem;">
                            <i class="bi bi-pencil-square me-1"></i> Manage Cost Sheet
                        </a>
                    </div>

                    <div class="modal-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover small align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Classification</th>
                                        <th>Formula Parameters Logged</th>
                                        <th class="text-end">Total Sum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($all_costs)): ?>
                                        <?php foreach ($all_costs as $item): ?>
                                            <tr>
                                                <td class="fw-bold text-dark-emphasis"><?= htmlspecialchars($item['cost_name']) ?></td>
                                                <td>
                                                    <span class="badge rounded-pill bg-light text-dark-emphasis border px-2.5 py-1 text-uppercase" style="font-size:0.65rem;">
                                                        <?= htmlspecialchars($item['cost_type']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted">
                                                    <?php 
                                                    if ($item['cost_type'] === 'material') {
                                                        echo htmlspecialchars($item['material_type']) . " | " . $item['quantity'] . " " . htmlspecialchars($item['unit']) . " @ $" . number_format($item['price_per_unit'], 2);
                                                    } elseif ($item['cost_type'] === 'furniture') {
                                                        echo htmlspecialchars($item['furniture_type']) . " | Qty: " . $item['quantity'] . " @ $" . number_format($item['price'], 2);
                                                    } elseif ($item['cost_type'] === 'labor') {
                                                        echo "$" . number_format($item['wage_per_day'], 2) . "/day x " . $item['estimated_days'] . " days";
                                                    } elseif ($item['cost_type'] === 'other') {
                                                        echo "Notes: " . htmlspecialchars($item['other_type']);
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-end fw-bold" style="color: #be7a3e;">
                                                    $<?= number_format($item['calculated_total'], 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-muted text-center py-4 italic">No financial tracking data calculated yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 p-3 rounded-2 d-flex justify-content-between align-items-center" style="background-color: #fdfaf6; border-right: 4px solid #dfa875;">
                            <span class="fw-bold small m-0" style="color: #3d2621;">Project Estimates Total Balance</span>
                            <h3 class="fw-bold m-0" style="color: #3d2621;">$<?= number_format($total_cost_projected, 2) ?></h3>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.querySelectorAll('.task-toggle-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.getAttribute('data-id');
                const isChecked = this.checked ? 1 : 0;
                const textLabel = this.nextElementSibling;
                const cardRow = this.closest('.list-group-item');

                if(isChecked) {
                    textLabel.classList.add('text-decoration-line-through');
                    cardRow.classList.add('bg-light', 'text-muted');
                } else {
                    textLabel.classList.remove('text-decoration-line-through');
                    cardRow.classList.remove('bg-light', 'text-muted');
                }

                const formData = new FormData();
                formData.append('update_id', taskId);
                formData.append('is_done', isChecked);
                fetch('toggle_task.php', { method: 'POST', body: formData });
            });
        });
        </script>
    </body>
</html>