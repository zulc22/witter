<?php

    function register($tag, $username, $email, $hashedpassword, $conn) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, tag) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashedpassword, $tag);
        $stmt->execute();
        $stmt->close();
        return true;
    }

?>