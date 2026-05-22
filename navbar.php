<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';

$currentPage = basename($_SERVER['PHP_SELF']);

$nav_project_query = $conn->query("SELECT project_id FROM projects WHERE is_active = 1 LIMIT 1");
$active_nav_project = $nav_project_query->fetch_assoc();
$nav_project_id = $active_nav_project ? $active_nav_project['project_id'] : null;
?>

<div class="d-flex flex-column flex-shrink-0 p-4 bg-white border-end h-100" style="width: 280px; min-height: 100vh;">
    <a href="home.php" class="d-flex align-items-center mb-4 me-md-auto text-decoration-none fw-bold" style="color: #3d2621; letter-spacing: 0.5px;">
        <span class="fs-5">RINZify</span>
    </a>
    
    <small class="text-uppercase fw-bold text-muted mb-3" style="font-size: 0.7rem; letter-spacing: 1px;">Project Manager</small>
    
    <hr class="mt-0 mb-4 border-secondary-subtle">

    <ul class="nav nav-pills flex-column mb-auto gap-2">      
        <li class="nav-item">
            <a href="dashboard.php" 
               class="nav-link fw-medium py-2.5 px-3 rounded-2 <?php if($currentPage == 'dashboard.php') echo 'active'; ?>"
               style="<?php echo ($currentPage == 'dashboard.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
               <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <?php if ($nav_project_id): ?>
                <a href="tasks.php?project_id=<?= $nav_project_id ?>" 
                   class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'tasks.php') ? 'active' : ''; ?>"
                   style="<?= ($currentPage == 'tasks.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                   <i class="bi bi-list-check me-2"></i> Task Checklist
                </a>
            <?php else: ?>
                <a href="dashboard.php" onclick="alert('Please select and work on a project first!');"
                   class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'tasks.php') ? 'active' : ''; ?>"
                   style="<?= ($currentPage == 'tasks.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                   <i class="bi bi-list-check me-2"></i> Task Checklist
                </a>
            <?php endif; ?>
        </li>

        <li class="nav-item">
            <?php if ($nav_project_id): ?>
                <a href="expenses.php?project_id=<?= $nav_project_id ?>" 
                   class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'expenses.php') ? 'active' : ''; ?>"
                   style="<?= ($currentPage == 'expenses.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                   <i class="bi bi-receipt-cutoff me-2"></i> Expense Tracker
                </a>
            <?php else: ?>
                <a href="dashboard.php" onclick="alert('Please select and work on a project first!');"
                   class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'expenses.php') ? 'active' : ''; ?>"
                   style="<?= ($currentPage == 'expenses.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                   <i class="bi bi-receipt-cutoff me-2"></i> Expense Tracker
                </a>
            <?php endif; ?>
        </li>

        <li class="nav-item">
            <?php if ($nav_project_id): ?>
                <a href="project_logs.php?project_id=<?= $nav_project_id ?>" 
                class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'project_logs.php') ? 'active' : ''; ?>"
                style="<?= ($currentPage == 'project_logs.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                <i class="bi bi-chat-dots me-2"></i> Project Logs
                </a>
            <?php else: ?>
                <a href="dashboard.php" onclick="alert('Please select and work on a project first!');"
                class="nav-link fw-medium py-2.5 px-3 rounded-2 <?= ($currentPage == 'project_logs.php') ? 'active' : ''; ?>"
                style="<?= ($currentPage == 'project_logs.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
                <i class="bi bi-chat-dots me-2"></i> Project Logs
                </a>
            <?php endif; ?>
        </li>

        <li class="nav-item">
            <a href="clients.php" 
               class="nav-link fw-medium py-2.5 px-3 rounded-2 <?php if($currentPage == 'clients.php') echo 'active'; ?>"
               style="<?php echo ($currentPage == 'clients.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
               <i class="bi bi-folder me-2"></i> Client Records
            </a>
        </li>
        
        <li class="nav-item">
            <a href="projects.php" 
               class="nav-link fw-medium py-2.5 px-3 rounded-2 <?php if($currentPage == 'projects.php') echo 'active'; ?>"
               style="<?php echo ($currentPage == 'projects.php') ? 'color: #dfa875 !important; background-color: #fdfaf6; font-weight: 600;' : 'color: #615553;'; ?>">
               <i class="bi bi-folder me-2"></i> Projects
            </a>
        </li>
    </ul>
</div>