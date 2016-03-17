<?php ob_start(); ?>
<?php include __DIR__ . '/head.php'; ?>
<?php include __DIR__ . '/nav.php'; ?>
    <div id="alertbox" role="alert"></div>
    <div class="hidden-xs" id="accountLarge">
        <div class="row">
            <div class="col-md-6" id="registersection">
                <h2>Register</h2>
                <p>Please register an account to add API keys.</p>
                <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=register'; ?>"
                      name="registerform">
                    <div class="form-group">
                        <label for="user_name">Username</label>
                        <input id="uname" class="form-control" type="text" pattern="[a-zA-Z0-9]{2,50}" name="user_name"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input id="email" class="form-control" type="email" name="user_email" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password_new">Password</label>
                        <input id="pwd" class="form-control" type="password" name="user_password_new" pattern=".{6,}"
                               autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label for="user_password_repeat">Confirm Password</label>
                        <input id="pwd2" class="form-control" type="password" name="user_password_repeat"
                               pattern=".{6,}"
                               required autocomplete="off">
                    </div>
                    <input type="submit" class="btn btn-success" name="register" value="Register"/>
                </form>
            </div>
            <div class="col-md-6" id="loginsection">
                <h2>Log in</h2>
                <p>Welcome back, please log in to view your account status.</p>
                <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" name="loginform">
                    <div class="form-group">
                        <label for="user_name">Username or Email</label>
                        <input type="text" class="form-control" id="login_input_username" name="user_name" required/>
                    </div>
                    <div class="form-group">
                        <label for="user_password">Password</label>
                        <input type="password" class="form-control" id="login_input_password" name="user_password"
                               required/>
                    </div>
                    <input type="submit" class="btn btn-primary" name="login" value="Log in"/>
                </form>
            </div>
        </div>
        <hr>
        <div id="temporarykeysection">
            <h2>Use temporary key</h2>
            <p>Here you can enter an EVE Online API key for temporary use, this key will not be saved in the database
                and will be deleted once you log out or close your browser.</p>
            <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=temp'; ?>"
                  name="temporaryform">
                <div class="form-group">
                    <label for="keyID">Key ID</label>
                    <input type="text" class="form-control" id="keyID" name="keyID" required>
                </div>
                <div class="form-group">
                    <label for="vCode">Verification Code</label>
                    <input type="text" class="form-control" id="vCode" name="vCode" required>
                </div>
                <input type="submit" class="btn btn-info" name="templogin" value="Use Key"/>
            </form>
        </div>
    </div>

    <div class="visible-xs" id="accountPageMobile">
        <button data-toggle="modal" data-target="#registerModal" type="button" class="btn btn-lg btn-success btn-block">
            Register
        </button>
        <br>
        <button data-toggle="modal" data-target="#loginModal" type="button" class="btn btn-lg btn-primary btn-block">Log
            in
        </button>
        <br>
        <button data-toggle="modal" data-target="#tempModal" type="button" class="btn btn-lg btn-info btn-block">Use
            Temporary Key
        </button>

        <div id="loginModal" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form name="loginform" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Login</h4>
                        </div>
                        <div class="modal-body">
                            <label for="login_input_username">Username or Email:</label>
                            <input type="text" class="form-control" id="login_input_username" name="user_name" required>
                            <br>
                            <label for="login_input_password">Password:</label>
                            <input type="password" class="form-control" id="login_input_password" name="user_password"
                                   required>
                        </div>
                        <div class="modal-footer">
                            <!--<a class="pull-left btn btn-warning" href="" data-toggle="modal" data-target="#lostpassModal">Forgot password</a>-->
                            <input type="submit" class="btn btn-primary" name="login" value="Log in"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="tempModal" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=temp'; ?>"
                          name="temporaryform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Enter API Key</h4>
                        </div>
                        <div class="modal-body">
                            <p>Here you can enter an EVE Online API key for temporary use, this key will not be saved in
                                the database and will be deleted once you log out or close your browser.</p>
                            <label for="login_input_username">Key ID</label>
                            <input type="text" class="form-control" id="keyID" name="keyID" required>
                            <br>
                            <label for="login_input_password">Verification Code</label>
                            <input type="text" class="form-control" id="vCode" name="vCode" required>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-info" name="login" value="Use Key"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="lostpassModal" class="modal fade">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=recover'; ?>"
                          name="lostpassform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Reset your password</h4>
                        </div>
                        <div class="modal-body">
                            <label for="login_input_email">Enter your Email:</label>
                            <input id="login_input_email" class="form-control" type="email" name="user_email" required/>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-warning" name="recover" value="Reset Password"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="registerModal" class="modal fade">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=register'; ?>"
                          name="registerform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Register</h4>
                        </div>
                        <div class="modal-body">
                            <label for="uname">Username:</label>
                            <input id="uname" class="form-control" type="text" pattern="[a-zA-Z0-9]{2,16}"
                                   name="user_name"
                                   required/>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="pwd">Password:</label>
                                    <input id="pwd" class="form-control" type="password" name="user_password_new"
                                           pattern=".{6,}" required autocomplete="off"/>
                                </div>
                                <br>
                                <div class="col-sm-6">
                                    <label for="pwd2">Confirm Password:</label>
                                    <input id="pwd2" class="form-control" type="password" name="user_password_repeat"
                                           pattern=".{6,}" required autocomplete="off"/>
                                </div>
                            </div>
                            <br>
                            <label for="email">Email:</label>
                            <input id="email" class="form-control" type="email" name="user_email" required/>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-success" name="register" value="Register"/>
                            <br>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="passwordModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <form method="post" action="<?php echo $_SERVER['SCRIPT_NAME'] . '?action=change'; ?>"
                          name="passwordform">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Change Password for
                                <?php
                                if (!empty($_SESSION['user_name'])) {
                                    echo $_SESSION['user_name'];
                                }
                                ?></h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="user_name" value="<?php
                            if (!empty($_SESSION['user_name'])) {
                                echo $_SESSION['user_name'];
                            }
                            ?>">
                            <label for="user_password_old">Current Password:</label>
                            <input id="user_password_old" class="form-control" type="password" name="user_password_old"
                                   pattern=".{6,}" required autocomplete="off"/>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label" for="pwd">New Password:</label>
                                    <input id="user_password_new" class="form-control" type="password"
                                           name="user_password_new" pattern=".{6,}" required autocomplete="off"/>
                                </div>
                                <div class="col-sm-6">
                                    <label for="pwd2">Confirm New Password:</label>
                                    <input id="user_password_repeat" class="form-control" type="password"
                                           name="user_password_repeat" pattern=".{6,}" required autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" name="changepassword" value="Change"/>
                            <br>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>


<?php include __DIR__ . '/foot.php'; ?>
    </body>
    </html>
<?php ob_flush(); ?>