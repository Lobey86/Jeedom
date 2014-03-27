<?php
if (!isConnect()) {
    include_file('mobile', '401', 'php');
    die();
}
sendVarToJS('otherUserId', init('chat_user_id'));
global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt" id="userChatList">';
$rightPanel .= '</ul>';
sendVarToJS('otherUserId', init('chat_user_id'));
?>

<?php
if (init('chat_user_id') != '') {
    $chatUser = chat::getUserInfo(init('chat_user_id'));
    if (is_array($chatUser)) {
        echo '<h2 style="position: relative; top : -10px;margin-top: 0px;margin-bottom: 0px;text-align: center;">Chat avec ' . $chatUser['Name'] . '</h2>';
    }
}
?>

<style>
    #ul_messageList
    {
        overflow-y: scroll;
        height: 250px;
    }
</style>

<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt" id="ul_messageList">

</ul>
<br/>

<label for="messageText"><strong>Message:</strong></label>
<input id="messageText" />

<?php

include_file('mobile', 'chat', 'js');
include_file('core', 'chatAdapter', 'js');
?>

