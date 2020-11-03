<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/register.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <link href="/static/css/required.css" rel="stylesheet">
        <title>Witter: What are you doing?</title>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
    </head>
    <body id="front">
    <div id="container">
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
        <?php
            if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] && $_POST['username']) {
                $email = htmlspecialchars(@$_POST['email']);
                $username = htmlspecialchars(@$_POST['username']);
                $tag = htmlspecialchars(@$_POST['fullname']);
                $password = @$_POST['password'];
                $passwordhash = password_hash(@$password, PASSWORD_DEFAULT);

                if($_POST['password'] !== $_POST['confirm']){ $error = "password and confirmation password do not match"; goto skip; }

                if(strlen($username) > 21) { $error = "your username must be shorter than 21 characters"; goto skip; }
                if(strlen($password) < 8) { $error = "your password must be at least 8 characters long"; goto skip; }
                if (!preg_match("/^[A-Za-z0-9-]+$/", $tag)) { $error = "your tag must only contain letters and numbers and dashes"; goto skip; }
                if(!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) { $error = "please include both letters and numbers in your password"; goto skip; }
                if(!isset($_POST['g-recaptcha-response'])){ $error = "captcha validation failed"; goto skip; }
                if(!validateCaptcha($config['recaptcha_secret'], $_POST['g-recaptcha-response'])) { $error = "captcha validation failed"; goto skip; }

                $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows) { $error = "there's already a user with that same name!"; goto skip; }

                if(register($tag, $username, $email, $passwordhash, $conn)) {
                    $_SESSION['siteusername'] = htmlspecialchars($username);
                    header("Location: manage/");
                } else {
                    $error = "There was an unknown error making your account.";
                }
                skip:
            }
        ?>
        <div id="content">
            <div class="wrapper">
                <center>
                    <div class="heading">
                        <h2>Join the Conversation</h2>
                    </div><br>
                    <form action="" method="post" id="submitform">
                        <fieldset>
                            <table class="input-form">
                                <tbody><tr>
                                    <th>
                                        <label for="user_name">Witter Tag</label>
                                    </th>
                                    <td class="col-field">
                                        <input autocomplete="off" class="text_field" id="user_name" name="fullname" size="30" tabindex="1" type="text">
                                    </td>
                                </tr>
                                <tr class="screen-name">
                                    <th>
                                        <label for="user_screen_name">Username</label>
                                    </th>
                                    <td class="col-field">
                                        <input autocomplete="off" class="text_field" id="user_screen_name" maxlength="15" name="username" size="15" tabindex="2" type="text">
                                    </td>
                                    <td class="col-help">
                                        <div class="label-box info" style="display: none;">
                                            <span id="screen_name_info">Pick a unique name on Twitter.</span>
                                        </div>
                                        <div class="label-box good" style="display: none;">
                                            Ok
                                        </div>
                                        <div class="label-box error" style="display: none;">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td colspan="2">
                                    <span id="screen_name_url">

                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="user_password">Password</label>
                                    </th>
                                    <td class="col-field">
                                        <input autocomplete="off" class="text_field" id="user_user_password" name="password" size="30" tabindex="3" type="password">
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="user_password">Confirm Password</label>
                                    </th>
                                    <td class="col-field">
                                        <input autocomplete="off" class="text_field" id="user_user_password" name="confirm" size="30" tabindex="3" type="password">
                                    </td>
                                </tr>
                                <tr class="email">
                                    <th>
                                        <label for="user_email">Email</label>
                                    </th>
                                    <td class="col-field">
                                        <input autocomplete="off" class="text_field" id="user_email" name="email" size="30" tabindex="4" type="text">
                                    </td>
                                </tr>
                                <tr class="email-updates">
                                    <th></th>
                                    <td colspan="2" class="col-field">
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td colspan="2"><input type="submit" value="Register" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin"> <?php if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } ?></td>
                                </tr>
                                </tbody></table>
                        </fieldset>
                    </form>
                </center>
                <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
            </div>
        </div>
    </div>
    </body>
</html>