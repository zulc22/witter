<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
    <link href="/static/css/required.css" rel="stylesheet">
    <title>Witter: What are you doing?</title>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <?php $user = getUserFromName(rhandleTag($_GET['n']), $conn); ?>
    <meta property="og:title" content="@<?php echo $user['username']; ?>" />
    <meta property="og:description"
          content="<?php echo $user['bio']; ?>" />
    <meta property="og:image" content="https://witter.spacemy.xyz/dynamic/pfp/<?php echo $user['pfp']; ?>" />
    <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
</head>
<body id="front">
<div id="container">
    <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php");
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(!isset($_SESSION['siteusername'])){ $error = "you are not logged in"; goto skipcomment; }
        if(!$_POST['comment']){ $error = "your comment cannot be blank"; goto skipcomment; }
        if(strlen($_POST['comment']) > 500){ $error = "your comment must be shorter than 500 characters"; goto skipcomment; }
        if(!isset($_POST['g-recaptcha-response'])){ $error = "captcha validation failed"; goto skipcomment; }
        if(!validateCaptcha($config['recaptcha_secret'], $_POST['g-recaptcha-response'])) { $error = "captcha validation failed"; goto skipcomment; }

        $stmt = $conn->prepare("INSERT INTO `weets` (realid, author, contents) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $uniqid, $_SESSION['siteusername'], $text);
        $uniqid = time() . uniqid();
        $text = htmlspecialchars($_POST['comment']);
        $stmt->execute();
        $stmt->close();
        skipcomment:
    }
    ?>
    <div id="content">
        <div class="wrapper">
            <?php if($user['banstatus'] == "suspended") { ?>
                <br>
                <div style='padding: 5px; border: 5px solid green;'>
                    <h4 id='noMargin'>
                        This user has been suspended.
                    </h4>
                </div>
                <?php die(); ?>
            <?php } ?>
            <div class="customtopRight">
                Name: <b><big><?php echo $user['username']; ?></big></b><br>
                <table id="cols">
                    <tr>
                        <th style="width: 33%;">&nbsp;</th>
                        <th style="width: 33%;">&nbsp;</th>
                        <th style="width: 33%;">&nbsp;</th>
                    </tr>
                    <tr>
                        <td><big><big><big><b><?php echo getFollowing($user['username'], $conn); ?></b></big></big></big><br><span id="blue">following</span></td>
                        <td><big><big><big><b><?php echo getFollowers($user['username'], $conn); ?></b></big></big></big><br><span id="blue">followers</span></td>
                        <td> </td>
                    </tr>
                </table><br>
                <center>
                    <?php if(isset($_SESSION['errorMsg'])) { echo "<div style='padding: 5px; border: 5px solid green;'><h4 id='noMargin'>" . $_SESSION['errorMsg']; unset($_SESSION['errorMsg']); echo "</h4></div><br>"; }?>
                    <?php
                        if(ifFollowing(rhandleTag($_GET['n']), @$_SESSION['siteusername'], $conn) == false) {?>
                            <a href="/follow.php?n=<?php echo $user['username']; ?>"><button>Follow</button></a>
                        <?php } else { ?>
                            <a href="/unfollow.php?n=<?php echo $user['username']; ?>"><button>Unfollow</button></a>
                        <?php }
                    ?>
                </center><br>

                <div class="altbg">
                    <b>Tweets</b><span id="floatRight"><?php echo getWeets(rhandleTag($_GET['n']), $conn); ?></span>
                </div><br>
                <?php
                $stmt = $conn->prepare("SELECT * FROM follow WHERE reciever = ?");
                $stmt->bind_param("s", $user['username']);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()) {
                    ?>
                    <a href="/u.php?n=<?php echo handleTag($row['sender']); ?>"><img style="width: 30px; height: 30px;" src="/dynamic/pfp/<?php echo getPFPFromUser($row['sender'], $conn); ?>"></a>
                    <?php
                }
                $stmt->close();
                ?>
            </div>
            <div class="customtopLeft">
                <img id="pfp" style="height: 10%; width: 13%;" src="/dynamic/pfp/<?php echo $user['pfp']; ?>"><h1><?php echo $user['username']; ?></h1>
                <table id="feed">
                    <tr>
                        <th style="width: 48px;">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                        $stmt = $conn->prepare("SELECT * FROM weets WHERE author = ?");
                        $stmt->bind_param("s", $tag);
                        $tag = rhandleTag($_GET['n']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows === 0) echo('There are no weets.');
                        while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <big><big><big>
                                <td>
                                    <img id="pfp" src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>">
                                </td>
                                <td><a href="/u.php?n=<?php echo handleTag($row['author']); ?>"><?php echo $row['author']; ?></a><div id="feedtext"><?php echo parseText($row['contents']); ?> </div>
                                    <small><?php echo time_elapsed_string($row['date']); ?> from <a href="/v.php?rid=<?php echo $row['realid']; ?>">web</a>
                                        <a href="like.php?id=<?php echo $row['id']; ?>">🧡</a> 💬<?php echo getComments($row['realid'], $conn); ?>
                                    </small>
                                </td>
                            </big></big></big>
                        </tr>
                    <?php
                        }
                        $stmt->close();
                    ?>
                </table>
            </div>
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
        </div>
    </div>
</div>
</body>
</html>