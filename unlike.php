<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>

<?php
$name = $_GET['id'];

if(!isset($_SESSION['siteusername']) || !isset($_GET['id'])) {
    $_SESSION['errorMsg'] = ("You are not logged in");
    goto skip;
}

$stmt = $conn->prepare("SELECT * FROM likes WHERE fromu = ? AND torid = ?");
$stmt->bind_param("ss", $_SESSION['siteusername'], $name);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0) {
    $_SESSION['errorMsg'] = ('You havent followed this person');
    goto skip;
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM likes WHERE fromu = ? AND torid = ?");
$stmt->bind_param("ss", $_SESSION['siteusername'], $name);
$stmt->execute();
$stmt->close();
skip:

header('Location: ' . $_SERVER['HTTP_REFERER']);
?>