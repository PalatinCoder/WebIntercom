<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<style type="text/css">
<!--
@media screen and (min-height: 630px) {
    .chat_wrapper .message_box {
        height: 500px;
    }
}
@media screen and (max-height: 629px) {
    .chat_wrapper .message_box {
        height: 350px;
    }
}
    
body {
    background: #DDD;
    }
    
.chat_wrapper {
	/*width: 500px;*/
	background: #CCCCCC;
	border: 1px solid #999999;
	padding: 10px;
	font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
}
.chat_wrapper .message_box {
	background: #888;
	/*height: 500px;*/
	overflow: auto;
	padding: 10px;
	border: 1px solid #999999;
}
.chat_wrapper .panel input{
	padding: 2px 2px 2px 5px;
}
.system_msg{color: #BDBDBD;font-style: italic;}
.user_name{font-weight:bold;}
.user_message{color: #88B6E0;}
    
.connection_box {
    padding-top: 10px;
    padding-bottom: 10px;
    }
-->
</style>
</head>
<body>	
<?php 
$colours = array('007AFF','FF7000','FF7000','15E25F','CFC700','CFC700','CF1100','CF00BE','F00');
$user_colour = array_rand($colours);
?>

<script src="jquery-2.1.3.min.js"></script>

<script language="javascript" type="text/javascript">  
$(document).ready(function(){
    $('#connect').click(function () {
        //create a new WebSocket object.
        var host = $('input#host').val();
        var wsUri = "ws://"+host+":9000/WebIntercom/server.php"; 	
        
        console.log(wsUri);
        websocket = new WebSocket(wsUri); 

        websocket.onopen = function(ev) { // connection is open 
            $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
        }
        
        $('#send-btn').click(function(event){ //use clicks message send button	
            var mymessage = $('#message').val(); //get message text
            var myname = $('#name').val(); //get user name

            if(myname == ""){ //empty name?
                alert("Enter your Name please!");
                return;
            }
            if(mymessage == ""){ //emtpy message?
                alert("Enter Some message Please!");
                return;
            }

            //prepare json data
            var msg = {
            message: mymessage,
            name: myname,
            color : '<?php echo $colours[$user_colour]; ?>'
            };
            //convert and send data to server
            websocket.send(JSON.stringify(msg));
        });
        
        $('#message').keypress(function(event) {
            if (event.which == 13) {
                $('#send-btn').click();
            }
        });

        //#### Message received from server?
        websocket.onmessage = function(ev) {
            var msg = JSON.parse(ev.data); //PHP sends Json data
            var type = msg.type; //message type
            var umsg = msg.message; //message text
            var uname = msg.name; //user name
            var ucolor = msg.color; //color

            if(type == 'usermsg') 
            {
                $('#message_box').append("<div><span class=\"user_name\" style=\"color:#"+ucolor+"\">"+uname+"</span> : <span class=\"user_message\">"+umsg+"</span></div>");
                $('#message_box').scrollTop($('#message_box').height());
            }
            if(type == 'system')
            {
                $('#message_box').append("<div class=\"system_msg\">"+umsg+"</div>");
            }

            $('#message').val(''); //reset text
        };

        websocket.onerror	= function(ev){$('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");}; 
        websocket.onclose 	= function(ev){$('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");}; 
    });
});
</script>    
<div class="chat_wrapper">
    <div class="panel">
                <div class="connection_box">
            <input type="text" id="host" placeholder="Hostname/-addresse" maxlength="15" />
            <input type="text" name="name" id="name" placeholder="Name" maxlength="20" />
            <button id="connect">Verbinden</button>    
        </div>

    </div>
    <div class="message_box" id="message_box"></div>
    <div class="panel">
        <input type="text" name="message" id="message" placeholder="Nachricht" maxlength="80" style="width:60%" autocomplete="off" />
        <input type="submit" id="send-btn" value="Senden"></input>
    </div>
</div>

</body>
</html>