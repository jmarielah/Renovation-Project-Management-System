<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">

    <?php include "head.php"; ?>

    <body style="background-color: #f7f3ee;">
        
        <div class="d-flex justify-content-center align-items-center vh-100 px-3">
            <div class="row g-0 w-100 bg-white rounded-4 shadow overflow-hidden" style="max-width: 950px;">
                
                <div class="col-12 col-md-6 p-4 p-lg-5 d-flex flex-column justify-content-center text-secondary">
                    
                    <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success text-center mb-3">
                        <?= $_SESSION['success']; ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <h2 class="text-dark fw-bold mb-4">Login</h2>

                    <form method="POST" action="authenticate.php">
                        <div class="mb-3">
                            <label Kristen for="email" class="form-label small fw-semibold text-muted">Email</label>
                            <input type="email" class="form-control py-2" id="email" name="email" placeholder="Enter email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                            <input type="password" class="form-control py-2" id="password" name="password" placeholder="Enter password" required>
                        </div>

                        <button type="submit" class="btn text-white w-100 py-2 fw-medium" style="background-color: #dfa875;">Log in</button>
                    </form>

                    <p class="text-center mt-4 mb-0 small text-muted">Don't have an account? 
                        <a href="#" class="text-decoration-none fw-bold" style="color: #dfa875;" data-bs-toggle="modal" data-bs-target="#register-modal">Register</a>
                    </p>
                </div>

                <div class="col-md-6 d-none d-md-block align-self-stretch" 
                    style="background-image: url('assets/design_example5.jpg'); 
                            background-size: cover; 
                            background-position: center; 
                            min-height: 100%;">
                </div>

            </div>
        </div>

        <div class="modal fade" id="register-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-secondary">
                        <form method="POST" action="register.php">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter full name"required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password</label>
                                <input type="password" class="form-control" name="password". placeholder="Enter password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold d-block">Role</label>
                                <input type="radio" class="btn-check" name="role" id="employee" value="employee" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary btn-sm px-3 me-1" for="employee">Employee</label>

                                <input type="radio" class="btn-check" name="role" id="admin" value="admin" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm px-3" for="admin">Admin</label>
                            </div>

                            <button type="submit" class="btn text-white w-100 mt-3 py-2" style="background-color: #dfa875;">Register</button>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>