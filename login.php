<?php session_start(); ?>

<!DOCTYPE html>
<html>

    <?php include "head.php";?>

    <body>
        <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center mb-3">
        <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!--login box-->
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="card p-4 justify-content-center align-items-center" id=login-card>
                <h3 class="text-center mb-4">LOGIN</h3>

                <form method="POST" action="authenticate.php">

                    <div class="row mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="row mb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-dark">Sign in</button>
                    </div>

                </form>
                <p>Don't have an account? <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#register-modal">Register</button> </p>

            </div>
        </div>

        <!--registration-->
        <div class="modal fade" id="register-modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <form method="POST" action="register.php">

                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label>Role</label>
                                <input type="radio" class="btn-check" name="role" id="employee" value="employee" autocomplete="off" checked>
                                <label class="btn btn-outline-dark" for="employee">Employee</label>

                                <input type="radio" class="btn-check" name="role" id="admin" value="admin" autocomplete="off">
                                <label class="btn btn-outline-dark" for="admin">Admin</label>
                            </div>

                        <button type="submit" class="btn btn-primary">Register</button>

                        </form>
                        
                    </div> 
                </div>
            </div>
        </div>
        
        <!--bootstrap script-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>

</html>