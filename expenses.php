<?php
session_start();
if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit(); }
include 'db_connection.php';
$project_id = intval($_GET['project_id']);

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM expenses WHERE expense_id = ? AND project_id = ?");
    $stmt->bind_param("ii", $del_id, $project_id);
    $stmt->execute();
    header("Location: expenses.php?project_id=" . $project_id);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $stmt = $conn->prepare("INSERT INTO expenses (project_id, description, amount, expense_type, expense_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $project_id, $_POST['description'], $_POST['amount'], $_POST['expense_type'], $_POST['expense_date']);
    $stmt->execute();
    header("Location: expenses.php?project_id=" . $project_id);
    exit();
}

if (isset($_POST['update_record'])) {
    $stmt = $conn->prepare("UPDATE expenses SET description=?, amount=?, expense_date=? WHERE expense_id=?");
    $stmt->bind_param("sdsi", $_POST['description'], $_POST['amount'], $_POST['expense_date'], $_POST['id']);
    $stmt->execute();
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$expenses = $conn->query("SELECT * FROM expenses WHERE project_id = $project_id ORDER BY expense_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, sans-serif;">
    <div class="d-flex">
        <?php include 'navbar.php'; ?>

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
                </div>

                <div class="d-flex justify-content-between align-items-center my-4">
                    <h3 class="fw-bold" style="color: #3d2621;">Expense Tracker</h3>
                    <button class="btn btn-sm text-white px-3 py-2" style="background-color: #dfa875;" data-bs-toggle="modal" data-bs-target="#expenseModal">
                        <i class="bi bi-plus-lg"></i> Add Expense
                    </button>
                </div>

                <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Description</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($exp = $expenses->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-medium text-dark"><?= htmlspecialchars($exp['description']) ?></td>
                                        <td><span class="badge rounded-pill text-capitalize" style="background-color: #f0eae1; color: #5c4d4a;"><?= htmlspecialchars($exp['expense_type']) ?></span></td>
                                        <td class="fw-bold" style="color: #3d2621;">P<?= number_format($exp['amount'], 2) ?></td>
                                        <td class="text-muted"><?= htmlspecialchars($exp['expense_date']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm text-primary edit-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal"
                                                    data-id="<?= $exp['expense_id'] ?>"
                                                    data-desc="<?= htmlspecialchars($exp['description']) ?>"
                                                    data-amt="<?= $exp['amount'] ?>"
                                                    data-type="<?= $exp['expense_type'] ?>"
                                                    data-date="<?= $exp['expense_date'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="expenses.php?project_id=<?= $project_id ?>&delete=<?= $exp['expense_id'] ?>" class="btn btn-sm text-danger"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="expenseModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content border-0">
                <div class="modal-header"><h5 class="modal-title">Log New Expense</h5></div>
                <div class="modal-body">
                    <input type="text" name="description" class="form-control mb-2" placeholder="Description" required>
                    <input type="number" step="0.01" name="amount" class="form-control mb-2" placeholder="Amount" required>
                    <select name="expense_type" class="form-select mb-2">
                        <option value="material">Material</option>
                        <option value="furniture">Furniture</option>
                        <option value="labor">Labor</option>
                        <option value="incidental">Incidental</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="date" name="expense_date" class="form-control mb-2" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_expense" class="btn text-white" style="background-color: #dfa875;">Save Expense</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content border-0">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header"><h5 class="modal-title">Edit Record</h5></div>
                <div class="modal-body" id="edit_modal_body">
                    </div>
                <div class="modal-footer">
                    <button type="submit" name="update_record" class="btn text-white" style="background-color: #dfa875;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            document.getElementById('edit_id').value = id;
            
            document.getElementById('edit_modal_body').innerHTML = `
                <input name="description" value="${button.getAttribute('data-desc')}" class="form-control mb-2" required>
                <input name="amount" value="${button.getAttribute('data-amt')}" class="form-control mb-2" required>
                <input type="date" name="expense_date" value="${button.getAttribute('data-date')}" class="form-control mb-2" required>
            `;
        });
    });
    </script>

    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>