<?php
session_start();

if (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
	header("Location: client.php");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["name"])) {
        header("Location: error.html");
        exit;
    }

    $username = $_POST['name'];
    //tags
    // $tags = array("", "", "");
    // $user_tag = $_POST['tag'];
    // if ($user_tag !== "") {
    //     array_push($tags, $user_tag);
    //     if ($tags[0] == "") {
    //         header("Location: error.html");
    //         exit;
    //     }
    // }
    

    if (isset($_FILES['userimage'])) {
        $allowed_types = array(
            'gif',
            'png',
            'jpeg',
            'jpg'
        );
        $extension = end(explode('.', $_FILES['userimage']['name']));
        if ((($_FILES['userimage']['type'] == "image/gif") || ($_FILES['userimage']['type'] == "image/jpeg") || ($_FILES['userimage']['type'] == "image/png"))) {

            $image_location = 'user_image/' . $username . '.' .$extension;
            if (!move_uploaded_file($_FILES['userimage']['tmp_name'], $image_location)) {
                header("Location: error.html");
            }

        } else {
            header("Location: error.html");
            exit;
        }
    } else {
        header("Location: error.html");
        exit;
    }

    require_once('xmlHandler.php');

    // create the chatroom xml file handler
    $xmlh = new xmlHandler("chatroom.xml");
    if (!$xmlh->fileExist()) {
        header("Location: error.html");
        exit;
    }

    // open the existing XML file
    $xmlh->openFile();

    // get the 'users' element
    $users_element = $xmlh->getElement("users");

    // create a 'user' element
    $user_element = $xmlh->addElement($users_element, "user");

    // add the user name
    $xmlh->setAttribute($user_element, "name", $_POST["name"]);
    // add the corresponding image
    $xmlh->setAttribute($user_element, "user_image", $image_location);
    // add the user tag
    $xmlh->setAttribute($user_element, "user_tag", "");
    // save the XML file
    $xmlh->saveFile();

    // set the name to the cookie
    setcookie("name", $_POST["name"]);
    $_SESSION['name'] = $_POST["name"];
    // set the tag to the cookie
    setcookie("tag", "");
    $_SESSION['tag'] = "";
    // Cookie done, redirect to client.php (to avoid reloading of page from the client)
    header("Location: client.php");
}
?>
