<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <link href="/static/css/required.css" rel="stylesheet">
    </head>
    <body style="background-image: none; background-color: white;">
        <table id="feed">
            <tr>
                <th style="width: 48px;">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT * FROM weets WHERE realid = ?");
            $stmt->bind_param('s', $_GET['i']);
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()) { ?>
            <tr>
                <big><big><big>
                            <td>
                                <img id="pfp" src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>">
                            </td>
                            <td><a href="/u.php?n=<?php echo handleTag($row['author']); ?>"><?php echo($row['author']); ?></a>
                                <?php if(returnVerifiedFromUsername($row['author'], $conn) != "") { ?> <span style="border-radius: 10px; background-color: deepskyblue; color: white; padding: 3px;"><?php echo(returnVerifiedFromUsername($row['author'], $conn)); ?></span> <?php } ?>
                                <div id="feedtext"><?php echo parseText($row['contents']); ?> </div>
                                <small><?php echo time_elapsed_string($row['date']); ?> from web
                                    <?php if(ifLiked($_SESSION['siteusername'], $row['id'], $conn) == true) { ?>
                                        <a href="unlike.php?id=<?php echo $row['id']; ?>"><img style="vertical-align: middle;" src="/static/witter-like.png">Unlike</a>
                                    <?php } else { ?>
                                        <a href="like.php?id=<?php echo $row['id']; ?>"><img style="vertical-align: middle;" src="/static/witter-like.png">Like</a>
                                    <?php } ?>
                                    <a href="/v.php?rid=<?php echo $row['realid']; ?>"><img style="vertical-align: middle;" src="/static/witter-reply.png">Reply</a>
                                    <?php echo getComments($row['realid'], $conn); ?><img style="vertical-align: middle;" src="/static/witter-replies.png">
                                    <a href="/home.php?text=https://witter.spacemy.xyz/embed/?i=<?php echo $row['realid']; ?>"><img style="vertical-align: middle;" src="/static/witter-reweet.png">Reweet</a>
                                </small><br>
                                <?php
                                $likes = getLikesReal($row['id'], $conn);
                                while($row = $likes->fetch_assoc()) {
                                    ?>
                                    <a href="/u.php?n=<?php echo handleTag($row['fromu']); ?>"><img style="width: 30px; height: 30px; margin-left: 2px;" id="pfp" src="/dynamic/pfp/<?php echo getPFPFromUser($row['fromu'], $conn); ?>"></a>&nbsp;
                                <?php } ?>
                            </td>
                        </big></big></big>
            </tr>
            <?php } ?>
        </table>
    </body>
</html>