chat = {};
chat.client = {};
chat.opts = {};
chat.opts.user = {};
chat.opts.user.Id = user_id;
chat.client.usersListChanged = function(userList) {
    printUserList(userList)
};

chat.client.sendMessage = function(_message) {
    chatAdapter.server.getUserInfo(_message.UserFromId, function(userInfo) {
        if (_message.UserFromId == otherUserId) {
            $('#ul_messageList').append('<li><img src="' + userInfo.ProfilePictureUrl + '" /> <span style="color : green;">' + _message.Message + '</span></li>');
            $('#ul_messageList').scrollTop($("#ul_messageList").prop('scrollHeight'));
        } else {
            notify(userInfo.Name, _message.Message);
        }
    });
};

$(document).on('pagecontainershow', function() {
    $(".rightpanel").panel().panel("open");

    $("#messageText").keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var message = $('#messageText').value();
            $('#messageText').value('');
            chatAdapter.server.getUserInfo(user_id, function(userInfo) {
                $('#ul_messageList').append('<li><img src="' + userInfo.ProfilePictureUrl + '" /> <span style="color : red;">' + message + '</span></li>');
                $('#ul_messageList').scrollTop($("#ul_messageList").prop('scrollHeight'));
            });
            chatAdapter.server.sendMessage(otherUserId, message);
        }
    });

    $('body').one('nodeJsConnect', function() {
        if (jeedom.chat.state === false) {
            chatAdapter = new jeedomChatAdapter();
            chatAdapter.init(chat, chatInitFinish);
            jeedom.chat.state = true;
        }else{
            chatInitFinish();
        }
        socket.on('refreshUserList', function(_connectUserList) {
            if (_connectUserList != null) {
                $.ajax({// fonction permettant de faire de l'ajax
                    type: "POST", // methode de transmission des données au fichier php
                    url: "core/ajax/chat.ajax.php", // url du fichier php
                    data: {
                        action: "refreshConnectUser",
                        connectUserList: json_encode(_connectUserList),
                    },
                    global: false,
                    dataType: 'json',
                    error: function(request, status, error) {
                        handleAjaxError(request, status, error,$('.ui-page-active #div_alert'));
                    },
                    success: function(data) { // si l'appel a bien fonctionné
                        if (data.state != 'ok') {
                            $('.ui-page-active #div_alert').showAlert({message: data.result, level: 'danger'});
                            return;
                        }
                    }
                });
            }
        });
    });
});

function chatInitFinish() {
    chatAdapter.server.getUsersList(printUserList);
    if (otherUserId != '') {
        chatAdapter.server.getMessageHistory(otherUserId, function(_history) {
            for (var i in _history) {
                chatAdapter.server.getUserInfo(_history[i].UserFromId, function(userInfo) {
                    var color = 'green';
                    if (_history[i].UserFromId == user_id) {
                        color = 'red';
                    }
                    $('#ul_messageList').append('<li><img src="' + userInfo.ProfilePictureUrl + '" /> <span style="color : ' + color + ';">' + _history[i].Message + '</span></li>');
                });
            }
            $('#ul_messageList').scrollTop($("#ul_messageList").prop('scrollHeight'));
        });
    }
}

function printUserList(_userList) {
    var ul = '';
    for (var i in _userList) {
        if (_userList[i].Id != user_id) {
            if (_userList[i].Status == 1) {
                ul += '<li><a href="index.php?v=m&p=chat&chat_user_id=' + _userList[i].Id + '"><i class="fa fa-circle pull-left"></i>' + _userList[i].Name + '</a></li>';
            } else {
                ul += '<li><a href="index.php?v=m&p=chat&chat_user_id=' + _userList[i].Id + '"><i class="fa fa-circle-o"></i>' + _userList[i].Name + '</a></li>';
            }
        }
    }
    $('#userChatList').html(ul);
    $('#userChatList').listview().listview('refresh');
}

