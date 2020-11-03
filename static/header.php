<div id="header" class="no-nav">
    <a href="<?php if(isset($_SESSION['siteusername'])) { ?>/home.php<?php } else { ?>/<?php } ?>" title="Witter: home" accesskey="1" id="logo">
        <img alt="Witter" src="/static/witter.png">
    </a>
    <?php if(isset($_SESSION['siteusername'])) {?>
        <div class="settings">
            <a href="/home.php">Home</a>&nbsp;&nbsp;<a href="/l.php">Find People</a>&nbsp;&nbsp;<a href="/manage">Your Profile</a>&nbsp;&nbsp;<a href="/help">Help</a>&nbsp;&nbsp;<a href="/signout.php">Sign out</a>
        </div>
    <?php } ?>
</div>