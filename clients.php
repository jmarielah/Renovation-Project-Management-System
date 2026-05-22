<?php
session_start();
if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit(); }
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO clients (name, contact, email, address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $contact, $email, $address);
    $stmt->execute();
    header("Location: clients.php");
    exit();
}

if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM clients WHERE client_id = $del_id");
    header("Location: clients.php");
    exit();
}

if (isset($_POST['update_client'])) {
    $stmt = $conn->prepare("UPDATE clients SET name=?, contact=?, email=?, address=? WHERE client_id=?");
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['contact'], $_POST['email'], $_POST['address'], $_POST['client_id']);
    $stmt->execute();
    header("Location: clients.php");
    exit();
}

$clients = $conn->query("SELECT * FROM clients ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<body style="background-color: #f7f3ee; color: #4a3e3d; font-family: system-ui, sans-serif;">
    <div class="d-flex">
        <?php include 'navbar.php'; ?>
        <main class="container-fluid px-4 pb-5">
            <div class="d-flex justify-content-between align-items-center my-4">
                <h3 class="fw-bold" style="color: #3d2621;">Client Records</h3>
                <button class="btn btn-sm text-white px-3 py-2" style="background-color: #dfa875;" data-bs-toggle="modal" data-bs-target="#clientModal">
                    <i class="bi bi-person-plus"></i> Add Client
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                <div class="p-3 bg-white border-bottom">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute" style="top: 50%; left: 12px; transform: translateY(-50%); color: #aaa;"></i>
                        <input type="text" id="clientSearch" 
                            class="form-control form-control-sm rounded-pill ps-5 py-2" 
                            placeholder="Search by name, email, or contact..." 
                            onkeyup="filterClients()"
                            style="border: 1px solid #e0dcd5; transition: all 0.3s;">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="clientTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = $clients->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 fw-medium"><?= htmlspecialchars($c['name']) ?></td>
                                    <td><?= htmlspecialchars($c['contact']) ?></td>
                                    <td><?= htmlspecialchars($c['email']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($c['address']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm text-primary edit-client-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editClientModal"
                                                data-id="<?= $c['client_id'] ?>"
                                                data-name="<?= htmlspecialchars($c['name']) ?>"
                                                data-contact="<?= htmlspecialchars($c['contact']) ?>"
                                                data-email="<?= htmlspecialchars($c['email']) ?>"
                                                data-address="<?= htmlspecialchars($c['address']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="clients.php?delete=<?= $c['client_id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Delete this client?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="clientModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content border-0">
                <div class="modal-header"><h5 class="modal-title">New Client Registration</h5></div>
                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-2" placeholder="Full Name" required>
                    <input type="text" name="contact" class="form-control mb-2" placeholder="Contact Number">
                    <input type="email" name="email" class="form-control mb-2" placeholder="Email Address">
                    <textarea name="address" class="form-control mb-2" placeholder="Physical Address"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_client" class="btn text-white" style="background-color: #dfa875;">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editClientModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content border-0">
                <input type="hidden" name="client_id" id="edit_client_id">
                <div class="modal-header"><h5 class="modal-title">Edit Client</h5></div>
                <div class="modal-body">
                    <input type="text" name="name" id="edit_name" class="form-control mb-2" placeholder="Full Name" required>
                    <input type="text" name="contact" id="edit_contact" class="form-control mb-2" placeholder="Contact">
                    <input type="email" name="email" id="edit_email" class="form-control mb-2" placeholder="Email">
                    <textarea name="address" id="edit_address" class="form-control mb-2" placeholder="Address"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_client" class="btn text-white" style="background-color: #dfa875;">Update Record</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.edit-client-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('edit_client_id').value = button.getAttribute('data-id');
            document.getElementById('edit_name').value = button.getAttribute('data-name');
            document.getElementById('edit_contact').value = button.getAttribute('data-contact');
            document.getElementById('edit_email').value = button.getAttribute('data-email');
            document.getElementById('edit_address').value = button.getAttribute('data-address');
        });
    });

    function filterClients() {
        const input = document.getElementById("clientSearch");
        const filter = input.value.toLowerCase();
        const table = document.getElementById("clientTable");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            const tds = tr[i].getElementsByTagName("td");
            let match = false;
            for (let j = 0; j < 3; j++) {
                if (tds[j] && tds[j].innerText.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
            tr[i].style.display = match ? "" : "none";
        }
    }
    </script>
</body>
</html>