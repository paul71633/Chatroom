<?php

if (!isset($_COOKIE["name"])) {
    header("Location: error.html");
    return;
}

// get the name from cookie
$name = $_COOKIE["name"];

// get the tag from cookie
$tag = $_COOKIE["tag"];
$user_tag = $_POST["tag"];

// get the message content
$message = $_POST["message"];
if (trim($message) == "") $message = "__EMPTY__";

$color = $_POST["color"];

require_once('xmlHandler.php');

// create the chatroom xml file handler
$xmlh = new xmlHandler("chatroom.xml");
if (!$xmlh->fileExist()) {
    header("Location: error.html");
    exit;
}

// create the following DOM tree structure for a message
// and add it to the chatroom XML file
//
// <message name="...">...</message>
//

$xmlh->openFile();
// Get the 'messages' element as the current element
$messages_element = $xmlh->getElement("messages");
// Create a 'message' element for each message
$message_element = $xmlh->addElement($messages_element, "message");
// get the 'user' elements
$user_elements = $xmlh->getElement("user");

$user_tags = $user_elements->getAttribute("user_tag");
$user_name = $user_elements->getAttribute("name");

// Add the name
$xmlh->setAttribute($message_element, "name", $name);
$xmlh->setAttribute($message_element, "color", $color);

//$_POST["tag"]是現在傳進來的, $tag是原本的
if ($_POST["tag"] == "") {
    $tags_all = $user_tags;
} else {
    $tags_all = $user_tags.", ".$_POST["tag"];
}

if (substr_count($tags_all, ",") == 3) {
    $splitted = explode(",", $tags_all);
    $splitted[0] = $splitted[1];
    $splitted[1] = $splitted[2];
    $splitted[2] = $splitted[3];
    $tags_all = $splitted[0].", ".$splitted[1].", ".$splitted[2];
}

if ($_POST["tag"] !== "" && $user_tags !== "") {
    $xmlh->setAttribute($message_element, "tag", $tags_all);
    $xmlh->setAttribute($user_elements, "user_tag", $tags_all);
} else if ($_POST["tag"] !== "" && $user_tags == "") {
    $xmlh->setAttribute($message_element, "tag", $_POST["tag"]);
    $xmlh->setAttribute($user_elements, "user_tag", $_POST["tag"]);
} else if ($_POST["tag"] == "") {
    $xmlh->setAttribute($message_element, "tag", $tags_all);
    $xmlh->setAttribute($user_elements, "user_tag", $tags_all);
} 

// foreach ($messages_element->childNodes as $message_child) {
//     $message_name = $message_child->getAttribute("name");
//     $message_tag = $message_child->getAttribute("tag");
//     if ($message_name == $user_name) {
//         $message_tag = $tags_all;
//     }
// }

// Add the content of the message
$xmlh->addText($message_element, $message);
$xmlh->saveFile();

if ($_POST["tag"] !== "" && $user_tags !== "") {
    setcookie("tag", $user_tags.", ".$_POST["tag"]);
    $_SESSION['tag'] = $user_tags.", ".$_POST["tag"];
} else if ($_POST["tag"] !== "" && $user_tags == "") {
    setcookie("tag", $_POST["tag"]);
    $_SESSION['tag'] = $_POST["tag"];
}

header("Location: client.php");

?>
