<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit(); }
include 'db_connection.php';

$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

// Verify Project Access
$verify = $conn->query("SELECT * FROM projects WHERE project_id = $project_id AND is_active = 1 LIMIT 1");
$current_project = $verify->fetch_assoc();

// Handle New Log Posting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_update'])) {
    $message = trim($_POST['message']);
    $user_id = $_SESSION['userID'];
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO project_logs (project_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $project_id, $user_id, $message);
        $stmt->execute();
        header("Location: project_logs.php?project_id=" . $project_id);
        exit();
    }
}

// Fetch Log History
// Changed 'u.username' to 'u.name' to match your 'users' table structure
$logs_res = $conn->prepare("
    SELECT l.*, u.name 
    FROM project_logs l 
    JOIN users u ON l.user_id = u.user_id 
    WHERE l.project_id = ? 
    ORDER BY l.created_at DESC
");
$logs_res->bind_param("i", $project_id);
$logs_res->execute();
$logs = $logs_res->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, -apple-system, sans-serif;">
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
                    <h3 class="fw-bold mt-2" style="color: #3d2621;">Project Logs</h3>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-top" style="top: 24px;">
                            <h5 class="fw-bold mb-3" style="color: #3d2621;">Post Update</h5>
                            <form method="POST">
                                <textarea name="message" class="form-control mb-3 rounded-2 shadow-none" rows="5" placeholder="Share progress, notes, or feedback..." required></textarea>
                                <button type="submit" name="post_update" class="btn btn-sm w-100 text-white fw-medium py-2 rounded-2" style="background-color: #dfa875; border: none;">
                                    <i class="bi bi-send me-1"></i> Post to Log
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                            <h5 class="fw-bold mb-4" style="color: #3d2621;">Communication History</h5>
                            <?php while ($log = $logs->fetch_assoc()): ?>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark-emphasis small">
                                            <?= htmlspecialchars($log['name'] ?? 'Unknown User') ?>
                                        </span>
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            <?= date('M d, Y • h:i A', strtotime($log['created_at'])) ?>
                                        </span>
                                    </div>
                                    <p class="mb-0 small text-secondary lh-base">
                                        <?= nl2br(htmlspecialchars($log['message'] ?? '')) ?>
                                    </p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>