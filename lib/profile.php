<?php

function getUserFromId($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
	if($result->num_rows === 0) return('That user does not exist.');
	$stmt->close();

	return $user;
}

function ifFollowing($reciever, $sender, $connection) {
    $stmt = $connection->prepare("SELECT * FROM follow WHERE reciever = ? AND sender = ?");
    $stmt->bind_param("ss", $reciever, $sender);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1) return true;
    $stmt->close();
}

function getWeetFromRID($tag, $connection) {
    $stmt = $connection->prepare("SELECT * FROM weets WHERE realid = ?");
    $stmt->bind_param("i", $tag);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user;
}

function getGroupFromId($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) { $group['name'] = "None"; $group['id'] = 0; };
    $stmt->close();

    return $user;
}

function getInfoFromBlog($id, $connection) {
        $stmt = $connection->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) return('That blog does not exist.');
    $stmt->close();

    return $user;
}

function archiveAllUserInfo($username, $connection) {
    $stmt = $connection->prepare("UPDATE comments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE blogs SET message = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE blogcomments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE groupcomments SET comment = '[archived]' WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("UPDATE groups SET description = '[archived]', name = '[archived]', pic = '51zLZbEVSTL._AC_SX679_.jpg' WHERE owner = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    return true;
}

function getAllFileSize($username, $conn) {
    $stmt = $conn->prepare("SELECT * FROM files WHERE owner = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $filesize = 0;
    while($row = $result->fetch_assoc()) {
        $filesize = $filesize + filesize("../dynamic/files/" . $row['filename']);
    }
    $stmt->close();
    return $filesize;
}

function delPostsFromUser($username, $conn) {
    $stmt = $conn->prepare("DELETE FROM weets WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM replies WHERE author = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();

    return true;
}

function delAccount($username, $connection) {
        $stmt = $connection->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function isAdmin($username, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND specialtag = '🛡️'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0) { return false; } else { return true; }
    $stmt->close();
}

function deleteComment($id, $conn) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function pinComment($id, $conn) {
    $stmt = $conn->prepare("UPDATE comments SET status = 'p' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function suspendUser($username, $conn) {
    $stmt = $conn->prepare("UPDATE users SET banstatus = 'suspended' WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function unsuspendUser($username, $conn) {
    $stmt = $conn->prepare("UPDATE users SET banstatus = 'fine' WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function unpinComment($id, $conn) {
    $stmt = $conn->prepare("UPDATE comments SET status = 'n' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function getUserFromName($name, $connection) {
        $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if($result->num_rows === 0) return('That user does not exist.');
    $stmt->close();

    return $user;
}

function getPosts($name, $connection) {
    $stmt = $connection->prepare("SELECT id FROM reply WHERE author = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $number = 0;
    while($row = $result->fetch_assoc()) {
        $number++;
    }
    return $number;
    $stmt->close();
}

function getIDFromUser($name, $connection) {
    $stmt = $connection->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
    }
    return $id;
    $stmt->close();
}

function getLikesFromBlog($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM bloglikes WHERE toid = ? AND type = 'l'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result); 
    $stmt->close();

    return $rows;
}

function getDislikesFromBlog($id, $connection) {
    $stmt = $connection->prepare("SELECT * FROM bloglikes WHERE toid = ? AND type = 'd'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result); 
    $stmt->close();

    return $rows;
}

function getNameFromUser($id, $connection) {
    $stmt = $connection->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $id = $row['username'];
    }
    return $id;
    $stmt->close();
}

function ReturnVerifiedFromUsername($username, $connection) {
    $stmt = $connection->prepare("SELECT specialtag FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $tag = $row['specialtag'];
    }
    return $tag;
    $stmt->close();
}

function getPFPFromUser($name, $connection) {
    $stmt = $connection->prepare("SELECT pfp FROM users WHERE username = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $pfp = $row['pfp'];
    }
    return $pfp;
    $stmt->close();
}

function updateCategoryTime($id, $conn) {
    $stmt = $conn->prepare("UPDATE categories SET lastmodified = now() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    return true;
}

function updateThreadTime($id, $conn) {
    $stmt = $conn->prepare("UPDATE threads SET lastmodified = now() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    return true;
}

function updateSteamURL($url, $username, $connection) {
    $stmt = $connection->prepare("UPDATE users SET steamurl = ? WHERE username = ?");
    $stmt->bind_param("ss", $url, $username);
    $stmt->execute();
    $stmt->close();
}

function convertYoutube($string) {
	return preg_replace(
		"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
		"<iframe width='290' height='200' src='//www.youtube.com/embed/$2' allowfullscreen></iframe>",
		$string
	);
}


function parseEmoticons($input) {
    $find = array(":troll:", ":nes:", ":cookie:", ":cookiemonster:", ":dance:", ":mac:", ":jon:");
    $replace = array(" <img src='/static/troll.png'> ", " <img src='/static/nes.gif'> ", " <img src='/static/cookie.gif'> ", " <img src='/static/CookieMonster.gif'> ", " <img src='/static/dance.gif'> ", " <img src='/static/macemoji.png'> ", " <img src='/static/jonnose.png'> ");
    $input = str_replace($find, $replace, $input);
    return $input;
}

function parseText($text) {
    $text = preg_replace_callback('#(?:https?://\S+)|(?:www.\S+)|(?:\S+\`\S+)#', function($arr)
    {
        if(strpos($arr[0], 'https://') !== 0)
        {
            $arr[0] = '' . $arr[0];
        }
        $url = parse_url($arr[0]);

        // images
        if(preg_match('#\.(png|jpg|gif)$#', $url['path']))
        {
            return '<img src="'. $arr[0] . '" />';
        }
        // youtube
        if(in_array($url['host'], array('www.youtube.com', 'youtube.com'))
            && $url['path'] == '/watch'
            && isset($url['query']))
        {
            parse_str($url['query'], $query);
            return sprintf('<iframe class="embedded-video" src="http://www.youtube.com/embed/%s" allowfullscreen></iframe>', $query['v']);
        }

        if(in_array($url['host'], array('www.bitview.net', 'bitview.net'))
            && $url['path'] == '/watch.php'
            && isset($url['query']))
        {
            parse_str($url['query'], $query);
            return sprintf('<iframe id="embedplayer" src="http://www.bitview.net/embed.php?v=%s" width="480" height="72" allowfullscreen scrolling="off" frameborder="0"></iframe>', $query['v']);
            //<iframe id="embedplayer" src="http://www.bitview.net/embed.php?v=TRVF8uiPJZ0" width="448" height="382" allowfullscreen scrolling="off" frameborder="0"></iframe>
        }

        if(in_array($url['host'], array('witter.spacemy.xyz'))
            && $url['path'] == '/embed/'
            && isset($url['query']))
        {
            parse_str($url['query'], $query);
            return sprintf('<iframe scrolling="no" frameborder="0" style="overflow: hidden;" src="https://witter.spacemy.xyz/embed/?i=%s" height="200" width="495" title="Reweet"></iframe>', $query['i']);
            //<iframe id="embedplayer" src="http://www.bitview.net/embed.php?v=TRVF8uiPJZ0" width="448" height="382" allowfullscreen scrolling="off" frameborder="0"></iframe>
        }
        //links
        return sprintf('<a href="%1$s">%1$s</a>', $arr[0]);
    }, $text);
    $text = preg_replace("/@([a-zA-Z0-9-]+|\\+\\])/", "<a href='/u.php?n=$1'>@$1</a>", $text);
    $text = str_replace(PHP_EOL, "<br>", $text);

    return $text;
}

function stripURLTHingies($url) {
    $replace = array("https://steamcommunity.com/id/", "/");
    return str_replace($replace, "", $url);
}

function redirectToLogin() {
    header("Location: ../login.php");
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * -1000000000000;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function handleTag($text) {
    $text = str_replace(" ", "-", $text);
    return $text;
}

function rhandleTag($text) {
    $text = str_replace("-", " ", $text);
    return $text;
}

function getWeets($author, $conn) {
    $stmt = $conn->prepare("SELECT * FROM weets WHERE author = ?");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getFollowing($author, $conn) {
    $stmt = $conn->prepare("SELECT * FROM follow WHERE sender = ?");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getFollowers($author, $conn) {
    $stmt = $conn->prepare("SELECT * FROM follow WHERE reciever = ?");
    $stmt->bind_param("s", $author);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getLikes($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE torid = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function ifLiked($user, $id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE torid = ? AND fromu = ?");
    $stmt->bind_param("is", $id, $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1) { return true; } else { return false; }
    $stmt->close();
}

function getComments($toc, $conn) {
    $stmt = $conn->prepare("SELECT * FROM replies WHERE toc = ?");
    $stmt->bind_param("s", $toc);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = mysqli_num_rows($result);
    $stmt->close();

    return $rows;
}

function getLikesReal($id, $conn) {
    $stmt = $conn->prepare("SELECT * FROM likes WHERE torid = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
    $stmt->close();
}

?>