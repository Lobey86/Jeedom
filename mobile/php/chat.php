<?php
if (!isConnect()) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('otherUserId', init('chat_user_id'));
global $rightPanel;
$rightPanel = '<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt" id="userChatList">';
$rightPanel .= '</ul>';
?>

<?php
if (init('chat_user_id') != '') {
    $chatUser = chat::getUserInfo(init('chat_user_id'));
    if (is_array($chatUser)) {
        echo '<h2 style="position: relative; top : -10px;margin-top: 0px;margin-bottom: 0px;text-align: center;">{{Chat avec}} ' . $chatUser['Name'] . '</h2>';
    }
}
?>

<ul data-role="listview" data-theme="a" data-dividertheme="a" class="ui-icon-alt" id="ul_messageList" style="margin-bottom: 50px;">

</ul>
<br/>
<div style="position: fixed; bottom: 20px;width : 100%;left:0px;background: white;">
    <label for="messageText"><strong>{{Message:}}</strong></label>
    <input id="messageText"/>
</div>
<?php
include_file('mobile', 'chat', 'js');
include_file('core', 'chatAdapter', 'js');
?>

