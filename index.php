<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/register.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <link href="/static/css/required.css" rel="stylesheet">
        <title>Witter: What are you doing?</title>
    </head>
    <body id="front">
        <div id="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <div id="content">
                <div class="wrapper">
                    <div class="customtopRight">
                        <form method="post" id="signin">
                            <fieldset class="common-form standard-form">
                                <legend>Please sign in</legend>
                                <?php
                                if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] && $_POST['username']) {
                                    $email = htmlspecialchars(@$_POST['email']);
                                    $username = htmlspecialchars(@$_POST['username']);
                                    $password = @$_POST['password'];
                                    $passwordhash = password_hash(@$password, PASSWORD_DEFAULT);

                                    $stmt = $conn->prepare("SELECT password FROM `users` WHERE username = ?");
                                    $stmt->bind_param("s", $username);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if(!mysqli_num_rows($result)){ { $error = "incorrect username or password"; goto skip; } }
                                    $row = $result->fetch_assoc();
                                    $hash = $row['password'];

                                    if(!password_verify($password, $hash)){ $error = "incorrect username or password"; goto skip; }
                                    $_SESSION['siteusername'] = $username;

                                    header("Location: manage/");
                                }
                                skip:

                                if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } ?>
                                <p>
                                    <label class="inside" tabindex="1" for="username">user name (not witter tag):</label>
                                    <input type="text" id="username" name="username" value="" title="username">
                                </p>
                                <p>
                                    <label class="inside" tabindex="2" for="password">password:</label>
                                    <input type="password" id="password" name="password" value="" title="password">
                                </p>
                                <p class="remember">
                                    <input type="checkbox" id="remember" name="remember_me" value="1">
                                    <label for="remember">Remember me</label>
                                </p>
                                <p class="submit">
                                    <input type="submit" id="signin_submit" value="Sign In »">
                                </p>
                                <p class="forgot">
                                    Forgot password?                <a id="resend_password_link" href="">Click here</a>.
                                </p>
                            </fieldset>
                        </form><br>
                        <center><a href="https://discord.gg/J5ZDsak">Join the Discord server</a></center>
                    </div>
                    <div class="customtopLeft">
                        <h2 id="noMargin">What is Witter?</h2><br><br>
                        <img alt="What is Witter?" class="tour" src="/static/tour_1.gif" width="508" height="154"><br>
                        <p class="teaser">
                            A modern opensource recreation of Twitter during 2009 and 2010. Most of the CSS for this comes from web archive. This is still <b>HEAVILY</b> in development, so except some bugs along the way.
                        </p>
                        <div class="intro">
                            <p><a id="signup_submit" class="join" href="/register.php">Get Started—Join!</a></p>
                        </div>
                    </div>
                    <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
                </div>
            </div>
        </div>
    </body>
</html>