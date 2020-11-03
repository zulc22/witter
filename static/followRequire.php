<?php
    $stmt = $conn->prepare("SELECT * FROM follow WHERE reciever = ?");
    $stmt->bind_param("s", $_SESSION['siteusername']);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
?>
    <a href="/u.php?n=<?php echo handleTag($row['sender']); ?>"><img style="width: 30px; height: 30px;" src="/dynamic/pfp/<?php echo getPFPFromUser($row['sender'], $conn); ?>"></a>
<?php
        }
    $stmt->close();
?>