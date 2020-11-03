<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['siteusername'])) {
    header("Location: ../index.php");
}

if(isAdmin($_SESSION['siteusername'], $conn) == false) {
    die("Ur not admin");
} else {
    if(@$_POST['suspend']) {
        suspendUser($_POST['subject'], $conn);
        echo "Suces<br>";
    } else if(@$_POST['unsuspend']) {
        unsuspendUser($_POST['subject'], $conn);
        echo "Suces<br>";
    } else if(@$_POST['del']) {
        delPostsFromUser($_POST['subject'], $conn);
        echo "succes<br>";
    }
}

function _getServerLoadLinuxData()
{
    if (is_readable("/proc/stat"))
    {
        $stats = @file_get_contents("/proc/stat");

        if ($stats !== false)
        {
            // Remove double spaces to make it easier to extract values with explode()
            $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

            // Separate lines
            $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
            $stats = explode("\n", $stats);

            // Separate values and find line for main CPU load
            foreach ($stats as $statLine)
            {
                $statLineData = explode(" ", trim($statLine));

                // Found!
                if
                (
                    (count($statLineData) >= 5) &&
                    ($statLineData[0] == "cpu")
                )
                {
                    return array(
                        $statLineData[1],
                        $statLineData[2],
                        $statLineData[3],
                        $statLineData[4],
                    );
                }
            }
        }
    }

    return null;
}

// Returns server load in percent (just number, without percent sign)
function getServerLoad()
{
    $load = null;

    if (stristr(PHP_OS, "win"))
    {
        $cmd = "wmic cpu get loadpercentage /all";
        @exec($cmd, $output);

        if ($output)
        {
            foreach ($output as $line)
            {
                if ($line && preg_match("/^[0-9]+\$/", $line))
                {
                    $load = $line;
                    break;
                }
            }
        }
    }
    else
    {
        if (is_readable("/proc/stat"))
        {
            // Collect 2 samples - each with 1 second period
            // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
            $statData1 = _getServerLoadLinuxData();
            sleep(1);
            $statData2 = _getServerLoadLinuxData();

            if
            (
                (!is_null($statData1)) &&
                (!is_null($statData2))
            )
            {
                // Get difference
                $statData2[0] -= $statData1[0];
                $statData2[1] -= $statData1[1];
                $statData2[2] -= $statData1[2];
                $statData2[3] -= $statData1[3];

                // Sum up the 4 values for User, Nice, System and Idle and calculate
                // the percentage of idle time (which is part of the 4 values!)
                $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

                // Invert percentage to get CPU time, not idle time
                $load = 100 - ($statData2[3] * 100 / $cpuTime);
            }
        }
    }

    return $load;
}
?>
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
            <form method="post" enctype="multipart/form-data" id="submitform">
                <b>Suspend User</b>
                <br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
                <input type="submit" name="suspend" value="Suspend">
            </form><br>

            <form method="post" enctype="multipart/form-data" id="submitform">
                <b>Unsuspend User</b>
                <br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
                <input type="submit" name="unsuspend" value="Unsuspend">
            </form><br>

            <form method="post" enctype="multipart/form-data" id="submitform">
                <b>Delete Posts from User</b>
                <br><input placeholder="Subject" type="text" name="subject" required="required" size="63"></b><br>
                <input type="submit" name="del" value="Delete">
            </form><br>
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
        </div>
    </div>
</div>
</body>
</html>