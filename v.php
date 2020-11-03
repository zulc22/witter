<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <link href="/static/css/required.css" rel="stylesheet">
    <title>Witter: What are you doing?</title>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <script>function onLogin(token){ document.getElementById('submitform').submit(); }</script>
    <?php $weet = getWeetFromRID($_GET['rid'], $conn); ?>
    <?php $user = getUserFromName($weet['author'], $conn); ?>
    <meta property="og:title" content="@<?php echo $user['username']; ?>" />
    <meta property="og:description"
          content="<?php echo $weet['contents']; ?>" />
    <meta property="og:image" content="https://witter.spacemy.xyz/dynamic/pfp/<?php echo $user['pfp']; ?>" />
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

        $stmt = $conn->prepare("INSERT INTO `replies` (author, contents, toc) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_SESSION['siteusername'], $text, $_GET['rid']);
        $uniqid = time() . uniqid();
        $text = htmlspecialchars($_POST['comment']);
        $stmt->execute();
        $stmt->close();
        skipcomment:
    }
    ?>
    <div id="content">
        <div class="wrapper">
            <div class="customtopRight">
                Name: <b><big><?php echo $_SESSION['siteusername']; ?></big></b><br>
                <table id="cols">
                    <tr>
                        <th style="width: 33%;">&nbsp;</th>
                        <th style="width: 33%;">&nbsp;</th>
                        <th style="width: 33%;">&nbsp;</th>
                    </tr>
                    <tr>
                        <td><big><big><big><b><?php echo getFollowing($_SESSION['siteusername'], $conn); ?></b></big></big></big><br><span id="blue">following</span></td>
                        <td><big><big><big><b><?php echo getFollowers($_SESSION['siteusername'], $conn); ?></b></big></big></big><br><span id="blue">followers</span></td>
                        <td> </td>
                    </tr>
                </table><br>
            </div>
            <div class="customtopLeft">
                <table id="feed">
                    <tr>
                        <th style="width: 48px;">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM weets WHERE realid = ?");
                    $stmt->bind_param("s", $_GET['rid']);
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
                                            <small><?php echo time_elapsed_string($row['date']); ?> from <a href="">web</a>
                                                <a href="like.php?id=<?php echo $row['id']; ?>">❤</a>
                                            </small>
                                        </td>
                                    </big></big></big>
                        </tr>
                        <?php
                    }
                    $stmt->close();
                    ?>
                </table>
                <?php if(isset($error)) { echo "<small style='color: red;'>" . $error . "</small>"; } ?> <span id="textlimit">0/500</span>
                <form method="post" enctype="multipart/form-data" id="submitform">
                    <textarea cols="32" style="width: 534px;" id="upltx" name="comment"></textarea><br>
                    <script src="/js/commd.js"></script>
                    <input style="float: right; font-size: 1.2em; margin-top: 5px; margin-right: -6px;" type="submit" value="reply" class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha_sitekey']; ?>" data-callback="onLogin">
                </form>
                <script src='/js/limit.js'></script><br>
                <table id="feed">
                    <tr>
                        <th style="width: 48px;">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                        $stmt = $conn->prepare("SELECT * FROM replies WHERE toc = ? ORDER BY id DESC");
                        $stmt->bind_param("s", $_GET['rid']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows === 0) echo('There are no reply weets.');
                        while($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <big><big><big>
                                        <td>
                                            <img id="pfp" src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>">
                                        </td>
                                        <td><a href="/u.php?n=<?php echo handleTag($row['author']); ?>"><?php echo $row['author']; ?></a><div id="feedtext"><?php echo parseText($row['contents']); ?> </div>
                                            <small><?php echo time_elapsed_string($row['date']); ?> from <a href="">web</a>

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