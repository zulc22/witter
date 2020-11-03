<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>

<?php
    $name = $_GET['n'];

    if(!isset($_SESSION['siteusername']) || !isset($_GET['n'])) {
        $_SESSION['errorMsg'] = ("You are not logged in");
        goto skip;
    }

    if($name == $_SESSION['siteusername']) {
        $_SESSION['errorMsg'] = ("You can't follow yourself");
        goto skip;
    }

    $stmt = $conn->prepare("SELECT * FROM follow WHERE sender = ? AND reciever = ?");
        $stmt->bind_param("ss", $_SESSION['siteusername'], $name);
        $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1) {
        $_SESSION['errorMsg'] = 'You already followed this person';
        goto skip;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO follow (sender, reciever, status) VALUES (?, ?, 'u')");
    $stmt->bind_param("ss", $_SESSION['siteusername'], $name);

    $stmt->execute();
    $stmt->close();

    skip:
    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>