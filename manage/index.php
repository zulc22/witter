<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/manage.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <link href="/static/css/required.css" rel="stylesheet">
    <title>Witter: What are you doing?</title>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <?php $user = getUserFromName($_SESSION['siteusername'], $conn); ?>
    <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
</head>
<body id="front">
    <div id="container">
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php");
            if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['bioset']) {
                updateUserBio($_SESSION['siteusername'], $_POST['bio'], $conn);
                header("Location: index.php");
            } else if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['pfpset']) {
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

                //This is terribly awful and i will probably put this in a function soon
                $target_dir = "../dynamic/pfp/";
                $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
                $target_name = md5_file($_FILES["fileToUpload"]["tmp_name"]) . "." . $imageFileType;

                $target_file = $target_dir . $target_name;

                $uploadOk = true;
                $movedFile = false;

                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif" ) {
                    $fileerror = 'unsupported file type. must be jpg, png, jpeg, or gif';
                    $uploadOk = false;
                }

                if (file_exists($target_file)) {
                    $movedFile = true;
                } else {
                    $movedFile = move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
                }

                if ($uploadOk) {
                    if ($movedFile) {
                        $stmt = $conn->prepare("UPDATE users SET pfp = ? WHERE `users`.`username` = ?;");
                        $stmt->bind_param("ss", $target_name, $_SESSION['siteusername']);
                        $stmt->execute();
                        $stmt->close();
                        header("Location: index.php");
                    } else {
                        $fileerror = 'fatal error';
                    }
                }
            }
        ?>
            <div id="content">
                <div class="wrapper">
                    <div class="customtopRight">
                        <img id="pfp" style="vertical-align: middle;" src="/dynamic/pfp/<?php echo $user['pfp']; ?>"> <b><big><big><?php echo $_SESSION['siteusername']; ?></big></big></b><br>
                        <table id="cols">
                            <tr>
                                <th style="width: 33%;">&nbsp;</th>
                                <th style="width: 33%;">&nbsp;</th>
                                <th style="width: 33%;">&nbsp;</th>
                            </tr>
                            <tr>
                                <td><big><big><big><b>5</b></big></big></big><br><span id="blue">following</span></td>
                                <td><big><big><big><b>5</b></big></big></big><br><span id="blue">followers</span></td>
                                <td><big><big><big><b>5</b></big></big></big><br><span id="blue">tweets</span></td>
                            </tr>
                        </table><br>
                        <div class="altbg">
                            <a href="/home.php">Home</a><br>
                            <a href="/pms.php">Private Messages [200]</a>
                        </div>
                    </div>
                    <div class="customtopLeft">
                        <form method="post" enctype="multipart/form-data">
                            <b>Profile Picture</b><br>
                            <input type="file" name="fileToUpload" id="fileToUpload">
                            <input type="submit" value="Upload Image" name="pfpset">
                        </form><br>
                        <form method="post" enctype="multipart/form-data">
                            <b>Bio</b><br>
                            <textarea cols="56" id="biomd" placeholder="Bio" name="bio"><?php echo $user['bio'];?></textarea><br>
                            <input name="bioset" type="submit" value="Set">
                        </form><br>
                    </div>
                    <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
                </div>
            </div>
        </div>
    </body>
</html>