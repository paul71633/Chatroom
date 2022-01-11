<?php

// get the name from cookie
// $name = "";
// if (isset($_COOKIE["name"])) {
//     $name = $_COOKIE["name"];
// }

print "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Message Page</title>
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script language="javascript" type="text/javascript">
        var loadTimer = null;
        var request;
        var datasize;
        var lastMsgID;
        var prevMessageLen = 0;

        function load() {
            var username = document.cookie;
            if (username.length == 0) {
                loadTimer = setTimeout("load()", 100);
                return;
            }

            loadTimer = null;
            datasize = 0;
            lastMsgID = 0;
            
            var node = document.getElementById("chatroom");
            node.style.setProperty("visibility", "visible", null);

            getUpdate();
        }

        function unload() {
            var username = document.cookie;
            if (username.length > 0) {
                //request = new ActiveXObject("Microsoft.XMLHTTP");
                request = new XMLHttpRequest();
                request.open("POST", "logout.php", true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(null);
            }
            if (loadTimer != null) {
                loadTimer = null;
                clearTimeout("load()", 100);
            }
        }

        function getUpdate() {
            //request = new ActiveXObject("Microsoft.XMLHTTP");
            request = new XMLHttpRequest();
            request.onreadystatechange = stateChange;
            request.open("POST", "server.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("datasize=" + datasize);
        }

        function stateChange() {
            if (request.readyState == 4 && request.status == 200 && request.responseText) {
                var xmlDoc;
                var parser = new DOMParser();
                xmlDoc = parser.parseFromString(request.responseText, "text/xml");
                datasize = request.responseText.length;
                updateChat(xmlDoc);
                getUpdate();
            }
        }

        function updateChat(xmlDoc) {

            //point to the message nodes
            var messages = xmlDoc.getElementsByTagName("message");
           
            var i;
            for (i = prevMessageLen; i < messages.length; ++i) {
                var username = messages[i].getAttribute("name");
                var color = messages[i].getAttribute("color");
                var userTag = messages[i].getAttribute("tag");
                showMessage(username, userTag, messages[i].textContent, color);
            }
            prevMessageLen = messages.length;
        }
        

        function showMessage(nameStr, tagStr, contentStr, color) {

                var node = document.getElementById("chattext");
                // Create the name text span
                var nameNode = document.createElementNS("http://www.w3.org/2000/svg", "tspan");

                // Set the attributes and create the text
                nameNode.setAttribute("x", 30);
                nameNode.setAttribute("dy", 20);
                nameNode.setAttribute("style", "fill:" + color);
                if(tagStr == null){
                    nameNode.appendChild(document.createTextNode(nameStr));
                }
                else {
                    nameNode.appendChild(document.createTextNode(nameStr + "(" + tagStr + ")"));
                }

                // Add the name to the text node
                node.appendChild(nameNode);

                // Create the score text span
                var conetentNode = document.createElementNS("http://www.w3.org/2000/svg", "tspan");

                // set the attributes and create text
                conetentNode.setAttribute("x", 250);
                conetentNode.setAttribute("style", "fill:" + color);
                
                // taking care of different content
                var currentCharPos = 0;
                while(currentCharPos<contentStr.length){
                    var urlpos = contentStr.indexOf("http",currentCharPos);
                    if(urlpos >= 0){
                        var upperContent = contentStr.substring(0,urlpos);
                        conetentNode.appendChild(document.createTextNode(upperContent));
                        currentCharPos = contentStr.indexOf(" ",urlpos);
                        if(currentCharPos == -1){
                            currentCharPos = contentStr.length;
                        }
                        var linkStr = contentStr.substring(urlpos, currentCharPos);
                        var link = document.createElementNS("http://www.w3.org/2000/svg", "a");
                        link.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', linkStr);
                        link.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:show', "new");
                        link.setAttribute("text-decoration", "underline");
                        link.setAttribute("style", "fill:" + color);
                        link.appendChild(document.createTextNode(linkStr));
                        conetentNode.appendChild(link);
                        contentStr = contentStr.substring(currentCharPos);
                        currentCharPos = 0;
                    }else{
                        conetentNode.appendChild(document.createTextNode(contentStr));
                        break;
                    }

                }
                node.appendChild(conetentNode);
        }

        </script>
    </head>

    <body style="text-align: left" onload="load()" onunload="unload()">
    <svg width="800px" height="2000px"
     xmlns="http://www.w3.org/2000/svg"
     xmlns:xhtml="http://www.w3.org/1999/xhtml"
     xmlns:xlink="http://www.w3.org/1999/xlink"
     xmlns:a="http://www.adobe.com/svg10-extensions" a:timeline="independent"
     >

        <g id="chatroom" style="visibility:hidden">                
        <rect width="520" height="2000" style="fill:white;stroke:red;stroke-width:2"/>
        <text x="260" y="40" style="fill:red;font-size:30px;font-weight:bold;text-anchor:middle">Chat Window</text> 
        <text id="chattext" y="45" style="font-size: 20px;font-weight:bold"/>
      </g>
  </svg>
  
<!--          <form action="">
            <input type="hidden" value="<?php print $name; ?>" id="username" />
        </form> -->

    </body>
</html>
