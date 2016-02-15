<?php

/**
 * Class OneFileLoginApplication
 *
 * An entire php application with user registration, login and logout in one file.
 * Uses very modern password hashing via the PHP 5.5 password hashing functions.
 * This project includes a compatibility file to make these functions available in PHP 5.3.7+ and PHP 5.4+.
 *
 * @author Panique
 * @link https://github.com/panique/php-login-one-file/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class OneFileLoginApplication
{
    /**
     * @var Object database connection
     */
    private $db_connection;

    /**
     * @var bool Login status of user
     */
    private $user_is_logged_in = false;

    /**
     * @var string System messages, likes errors, notices, etc.
     */
    public $feedback = '';


    /**
     * Runs the application
     */
    public function __construct()
    {
        $this->runApplication();
    }

    /**
     * This is basically the controller that handles the entire flow of the application.
     */
    public function runApplication()
    {
        // check is user wants to register
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'register') {
                $this->doRegistration();
            }/* elseif ($_GET['action'] === 'recover') {
                //$this->doPassRecovery();
                //$this->showPassRecoveryPage();
                */
            elseif ($_GET['action'] === 'temp') {
                $this->doTempLogin();
                /*} elseif ($_GET['action'] === 'change') {
                    $this->doPassChange();
                }*/
            }
            else {
                $this->loginStuff();
            }
        }else {
            // start the session, always needed!
            $this->loginStuff();
        }
    }

    private function loginStuff()
    {
        $this->doStartSession();
        // check for possible user interactions (login with session/post data or logout)
        $this->performUserLoginAction();
    }

    private function doTempLogin()
    {
        if ($this->checkTempDataNotEmpty()) {
            $this->doStartSession();
            $keyID = $_POST['keyID'];
            $vCode = $_POST['vCode'];
            $_SESSION['keyID'] = $keyID;
            $_SESSION['vCode'] = $vCode;
            $_SESSION['selectedCharacter'] = 0;
            $this->user_is_logged_in = true;
            header('Location: index.php');
            die();
        }
    }

    private function createDatabaseConnection()
    {
        try {
            $config = parse_ini_file('config/EVIEdatabase.ini');
            $DB_Host = $config['DB_Host'];
            $DB_Name = $config['DB_Name'];
            $DB_User = $config['DB_User'];
            $DB_Password = $config['DB_Password'];
        } catch (Exception $e) {
            return false;
        }
        try {
            $this->db_connection = new PDO('mysql:host=' . $DB_Host . ';dbname=' . $DB_Name . ';charset=utf8', $DB_User, $DB_Password);
            return true;
        } catch (PDOException $e) {
            $this->feedback = 'PDO database connection problem: ' . $e->getMessage();
        } catch (Exception $e) {
            $this->feedback = 'General problem: ' . $e->getMessage();
        }
        return false;
    }

    /**
     * Handles the flow of the login/logout process. According to the circumstances, a logout, a login with session
     * data or a login with post data will be performed
     */
    private function performUserLoginAction()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            $this->doLogout();
        } elseif (!empty($_SESSION['user_name']) && $_SESSION['user_is_logged_in']) {
            $this->doLoginWithSessionData();
        } elseif (isset($_POST['login'])) {
            $this->doLoginWithPostData();
        }
    }

    /**
     * Simply starts the session.
     * It's cleaner to put this into a method than writing it directly into runApplication()
     */
    private function doStartSession()
    {
        session_set_cookie_params(7200);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a marker (NOTE: is this method necessary ?)
     */
    private function doLoginWithSessionData()
    {
        $this->user_is_logged_in = true; // ?
    }

    /**
     * Process flow of login with POST data
     */
    private function doLoginWithPostData()
    {
        if ($this->checkLoginFormDataNotEmpty() && $this->createDatabaseConnection()) {
            $this->checkPasswordCorrectnessAndLogin();
        }
    }

    /**
     * Logs the user out
     */
    private function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->user_is_logged_in = false;
    }

    /**
     * The registration flow
     * @return bool
     */
    private function doRegistration()
    {
        if ($this->checkRegistrationData() && $this->createDatabaseConnection()) {
            $this->createNewUser();
        }
        // default return
        return false;
    }

    /*
    private function doPassChange()
    {
        $oldpass = '1';
        $newpass = '2';
        $newpass2 = '3';
        $username = '4';
        $oldpass = $_POST['user_password_old'];
        $newpass = $_POST['user_password_new'];
        $newpass2 = $_POST['user_password_repeat'];
        $username = $_POST['user_name'];
        if($newpass === $newpass2){
            $oldpasshash =  password_hash($oldpass, PASSWORD_DEFAULT);
            $this->createDatabaseConnection();
            $sql = 'SELECT user_password_hash, user_email FROM users WHERE user_name = "'.$username.'" LIMIT 1';
            $query = $this->db_connection->prepare($sql);
            $query->execute();
            $result_row = $query->fetchObject();
            if ($result_row) {
                $oldpasshash = $result_row->user_password_hash;
                if(password_verify($oldpass, $oldpasshash)){
                    $newpasshash = password_hash($newpass, PASSWORD_DEFAULT);
                    try{
                        $sql2 = 'UPDATE users SET user_password_hash = "'.$newpasshash.'" WHERE user_name = \''.$username.'\'';
                        $query2 = $this->db_connection->prepare($sql2);
                        $query2->execute();
                        echo '<script type="text/javascript">';
                        echo 'alert("Your password has been changed.")';
                        echo '</script>';
                        $this->loginStuff();
                        return true;
                    } catch (PDOException $e) {
                        echo '<script type="text/javascript">';
                        echo 'alert("Database Error")';
                        echo '</script>';
                    }
                }
                else {
                    echo '<script type="text/javascript">';
                    echo 'alert("Incorrect password entered.")';
                    echo '</script>';
                }
            }
        }
        else{
            echo '<script type="text/javascript">';
   			echo 'alert("Your password confirmation does not match!")';
   			echo '</script>';
        }
        return false;
    }
    */
    /*
    private function doPassRecovery()
    {
        try {
            $Mailconfig = parse_ini_file('config/EVIEmail.ini');
            $Mail_Host = $Mailconfig['Email_Host'];
            $Mail_Name = $Mailconfig['Email_User'];
            $Mail_User = $Mailconfig['Email_Password'];
        } catch (Exception $e) {
            echo '<script type="text/javascript">';
   			echo 'alert("Something went wrong, please consult an admin")';
   			echo '</script>';
            return false;
        }
        $this->createDatabaseConnection();
        $email = $_POST['user_email'];
        $sql = 'SELECT user_name, user_email FROM users WHERE user_email = "'.$email.'" LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->execute();
        $result_row = $query->fetchObject();
        if ($result_row) {
            $token = md5(uniqid(rand(), true));
            require '/mailer/PHPMailerAutoload.php';
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = $Mail_Host;
            $mail->SMTPAuth = true;
            $mail->Username = $Mail_Name;
            $mail->Password = $Mail_User;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('info@saturnserver.org', 'Saturn Server Network');
            $mail->addAddress($email, $result_row->user_name);
            $mail->addReplyTo('info@saturnserver.org', 'Saturn Server Network');
            //$mail->addCC('cc@example.com');
            $mail->addBCC('info@saturnserver.org');
            //$mail->addAttachment('/var/tmp/file.tar.gz');
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');
            $mail->isHTML(true);

            $mail->Subject = 'Password reset for user: '.$result_row->user_name;
            $mail->Body    = 'Hello '.$result_row->user_name.'.<br>
            A password reset has been requested on the Saturn Server Network website on '.date('l jS \of F Y h:i:s A').'.<br>
            <br>If it was you who requested this reset, then please click this link:<br>
            <a href="http://saturnserver.org/passreset.php?token='.$token.'">http://saturnserver.org/passreset</a><br>
            If you did not request a password reset, you can ignore this email.<br><br>
            Have a nice day!<br><br>
            The Saturn Server Network<br>
            <a href="http://saturnserver.org">http://saturnserver.org</a>

            ';

            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
                return false;
            } else {
                echo '<p class="text-center">Password reset mail has been sent.';
                $sql = 'INSERT INTO tokens (token, user_name, user_email) VALUES(:token, :user_name, :user_email)';
                $query = $this->db_connection->prepare($sql);
                $query->bindValue(':token', $token);
                $query->bindValue(':user_name', $result_row->user_name);
                $query->bindValue(':user_email', $email);
                $query->execute();
            }
            return true;
        }
        else {
            echo '<script type="text/javascript">';
   			echo 'alert("That account does not exist.")';
   			echo '</script>';
        }
        return false;
    }
    */

    /**
     * Validates the login form data, checks if username and password are provided
     * @return bool Login form data check success state
     */
    private function checkLoginFormDataNotEmpty()
    {
        if (!empty($_POST['user_name']) && !empty($_POST['user_password'])) {
            return true;
        } elseif (empty($_POST['user_name'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Username field was empty.")';
            echo '</script>';
        } elseif (empty($_POST['user_password'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Password field was empty.")';
            echo '</script>';
        }
        // default return
        return false;
    }

    private function checkTempDataNotEmpty()
    {
        if (!empty($_POST['keyID']) && !empty($_POST['vCode'])) {
            return true;
        } elseif (empty($_POST['keyID'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Key ID field was empty.")';
            echo '</script>';
        } elseif (empty($_POST['vCode'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Verification Code field was empty.")';
            echo '</script>';
        }
        // default return
        return false;
    }

    /**
     * Checks if user exits, if so: check if provided password matches the one in the database
     * @return bool User login success status
     */
    private function checkPasswordCorrectnessAndLogin()
    {
        // remember: the user can log in with username or email address
        $sql = 'SELECT user_name, user_email, user_password_hash
                FROM users
                WHERE user_name = :user_name OR user_email = :user_name
                LIMIT 1';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $_POST['user_name']);
        $query->execute();

        // Btw that's the weird way to get num_rows in PDO with SQLite:
        // if (count($query->fetchAll(PDO::FETCH_NUM)) == 1) {
        // Holy! But that's how it is. $result->numRows() works with SQLite pure, but not with SQLite PDO.
        // This is so crappy, but that's how PDO works.
        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            // using PHP 5.5's password_verify() function to check password
            if (password_verify($_POST['user_password'], $result_row->user_password_hash)) {
                // write user data into PHP SESSION [a file on your server]
                $_SESSION['user_name'] = $result_row->user_name;
                $_SESSION['user_email'] = $result_row->user_email;
                $_SESSION['user_is_logged_in'] = true;
                $this->user_is_logged_in = true;
                $sql2 = 'SELECT user_name, apikey_keyid, apikey_vcode
                FROM apikeys
                WHERE user_name = :user_name AND apikey_isactive = 1
                LIMIT 1';
                $query2 = $this->db_connection->prepare($sql2);
                $query2->bindValue(':user_name', $_SESSION['user_name']);
                $query2->execute();
                $result_row2 = $query2->fetchObject();
                if ($result_row2) {
                    $_SESSION['keyID'] = $result_row2->apikey_keyid;
                    $_SESSION['vCode'] = $result_row2->apikey_vcode;
                    $_SESSION['selectedCharacter'] = 0;
                }
                return true;
            } else {
                echo '<script type="text/javascript">';
                echo 'alert("Wrong password.")';
                echo '</script>';
            }
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("This user does not exist.")';
            echo '</script>';
        }
        // default return
        return false;
    }

    /**
     * Validates the user's registration input
     * @return bool Success status of user's registration data validation
     */
    private function checkRegistrationData()
    {
        // if no registration form submitted: exit the method
        if (!isset($_POST['register'])) {
            return false;
        }

        // validating the input
        if (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 16
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,16}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && strlen($_POST['user_password_new']) >= 6
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            // only this case return true, only this case is valid
            return true;
        } elseif (empty($_POST['user_name'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Username can not be empty")';
            echo '</script>';
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Password can not be empty")';
            echo '</script>';
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            echo '<script type="text/javascript">';
            echo 'alert("Password and password repeat are not the same")';
            echo '</script>';
        } elseif (strlen($_POST['user_password_new']) < 6) {
            echo '<script type="text/javascript">';
            echo 'alert("Password has a minimum length of 6 characters")';
            echo '</script>';
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            echo '<script type="text/javascript">';
            echo 'alert("Username cannot be shorter than 2 or longer than 16 characters")';
            echo '</script>';
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 16 characters")';
            echo '</script>';
        } elseif (empty($_POST['user_email'])) {
            echo '<script type="text/javascript">';
            echo 'alert("Email cannot be empty")';
            echo '</script>';
        } elseif (strlen($_POST['user_email']) > 64) {
            echo '<script type="text/javascript">';
            echo 'alert("Email cannot be longer than 64 characters")';
            echo '</script>';
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            echo '<script type="text/javascript">';
            echo 'alert("Your email address is not in a valid email format")';
            echo '</script>';
        } else {
            echo '<script type="text/javascript">';
            echo 'alert("An unknown error occurred.")';
            echo '</script>';
        }

        // default return
        return false;
    }

    /**
     * Creates a new user.
     * @return bool Success status of user registration
     */
    private function createNewUser()
    {
        // remove html code etc. from username and email
        $user_name = htmlentities($_POST['user_name'], ENT_QUOTES);
        $user_email = htmlentities($_POST['user_email'], ENT_QUOTES);
        $user_password = $_POST['user_password_new'];
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 char hash string.
        // the constant PASSWORD_DEFAULT comes from PHP 5.5 or the password_compatibility_library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = 'SELECT * FROM users WHERE user_name = :user_name OR user_email = :user_email';
        $query = $this->db_connection->prepare($sql);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':user_email', $user_email);
        $query->execute();

        // As there is no numRows() in SQLite/PDO (!!) we have to do it this way:
        // If you meet the inventor of PDO, punch him. Seriously.
        $result_row = $query->fetchObject();
        if ($result_row) {
            echo '<script type="text/javascript">';
            echo 'alert("Sorry, that username / email is already taken. Please choose another one.")';
            echo '</script>';
            return true;
        } else {
            $sql = 'INSERT INTO users (user_name, user_password_hash, user_email)
                    VALUES(:user_name, :user_password_hash, :user_email)';
            $query = $this->db_connection->prepare($sql);
            $query->bindValue(':user_name', $user_name);
            $query->bindValue(':user_password_hash', $user_password_hash);
            $query->bindValue(':user_email', $user_email);
            // PDO's execute() gives back TRUE when successful, FALSE when not
            // @link http://stackoverflow.com/q/1661863/1114320
            $registration_success_state = $query->execute();

            if ($registration_success_state) {
                echo '<script type="text/javascript">';
                echo 'alert("Your account has been created successfully. You can now log in.")';
                echo '</script>';
                //header("Location: /eve/account.php?char=0");
                //die();

                return true;
            } else {
                echo '<script type="text/javascript">';
                echo 'alert("Sorry, your registration failed. Please go back and try again.")';
                echo '</script>';
            }
        }
        // default return
        return false;
    }

    /**
     * Simply returns the current status of the user's login
     * @return bool User's login status
     */
    public function getUserLoginStatus()
    {
        return $this->user_is_logged_in;
    }
}

// run the application
$application = new OneFileLoginApplication();