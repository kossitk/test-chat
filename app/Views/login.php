<?php $title = 'Chat'; ?>

<?php ob_start(); ?>
    <link rel="stylesheet" href="/assets/css/login.css">
<?php $templateCss = ob_get_clean(); ?>


<?php ob_start(); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-xl-4">
                <div class="card panel-login">
                    <div class="card-header">
                        <div class="d-flex justify-content-around">
                            <a href="#" class="active" id="login-form-link">Login</a>
                            <a href="#" id="register-form-link">Register</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errorMessage) && strlen($errorMessage) > 0) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $errorMessage ?>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="login-form" method="post" role="form" style="display: block;">
                                    <div class="form-group">
                                        <input type="text" name="email" id="email" tabindex="1" class="form-control" placeholder="Email" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 mx-auto">
                                                <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form id="register-form" action="/register" method="post" role="form" style="display: none;">
                                    <div class="form-group">
                                        <input type="text" name="username" id="username" tabindex="1" class="form-control <?= isset($formErrors['username']) ? 'is-invalid': '' ?>" placeholder="Username" value="">
                                        <div class="invalid-feedback">Please provide a valid username.</div>
                                        <small class="form-text text-muted">
                                            Username contain alphabetic letters and digits (no space allowed), at least 3 characters.
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" tabindex="2" class="form-control <?= isset($formErrors['email']) ? 'is-invalid': '' ?>" placeholder="Email Address" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" tabindex="3" class="form-control <?= isset($formErrors['password']) ? 'is-invalid': '' ?>" placeholder="Password">
                                        <div class="invalid-feedback">Please provide a valid password.</div>
                                        <small class="form-text text-muted">
                                            Minimum eight characters, at least one uppercase letter, one lowercase letter, one number
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="confirm-password" id="confirm-password" tabindex="4" class="form-control <?= isset($formErrors['confirm-password']) ? 'is-invalid': '' ?>" placeholder="Confirm Password">
                                        <div class="invalid-feedback">Please retype the same password.</div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 mx-auto">
                                                <input type="submit" name="register-submit" id="register-submit" tabindex="5" class="form-control btn btn-register" value="Register Now">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $templateContent = ob_get_clean(); ?>

<?php ob_start(); ?>
    <script src="/assets/js/login.js"></script>
<?php $templateJavascript = ob_get_clean(); ?>

<?php require('base.php'); ?>