$(document).ready(function () {
    simpleChat.init();
});

var simpleChat = {
    data : {
        offset 	: 0,
        noActivity	: 0
    },

    // Init привязывает обработчики событий и устанавливает таймеры:

    init : function(){
        // реализуем смену комнат в чате
        $('#room').change(function() {
            simpleChat.data.offset = 0; // сбросим счетчик сообщений
            $('#chat').text(''); // очистим блок чата
            simpleChat.getChats(); // запросим новые данные
        });

        $('#snd-btn').click(function() {
            var data = $('#message').val(),
                room = $('#room').val();

            if(data.length == 0){
                return false;
            }

            var params = {
                room: room,
                data : data.replace(/</g,'&lt;').replace(/>/g,'&gt;')
            };

            post('/chat/send-message',params,function(response){
                if (!response.success) {
                } else {
                    $('#message').val('');
                    simpleChat.getChats();
                }
            });

            return false;
        });

        //зациклим обращение к серверу за поиском новых сообщений
        (function getChatsTimeoutFunction(){
            simpleChat.getChats(getChatsTimeoutFunction);
        })();

        //зациклим обращение к серверу за поиском новых сообщений
        (function getUsersTimeoutFunction(){
            simpleChat.getUsers(getUsersTimeoutFunction);
        })();
    },

    // Метод render генерирует разметку HTML,

    render : function(template, data){

        var tmp = [];

        switch(template) {
            case 'chat':
                tmp = [
                    '<tr><td>' + data.user + '['+ data.time +']:' + '</td>',
                    '<td>' + data.message + '</td></tr>'
                ];
                break;
            case 'user':
                let status = '<span class="badge btn btn-success">онлайн</span>';
                if (data.no_activity >= 10){
                   status = '<span class="badge btn btn-danger">офлайн</span>';
                }
                tmp = [
                    '<li class="list-group-item block-user">' +
                    status+
                        data.username +
                        '<br><span>последнее действия:<b>'+data.last_action_time+
                    '</b></span>' +
                        '<div class="block-avatar"><img src="/uploads/images/'+data.avatar+'"></div>'+
                    '</li>'
                ];

                break;
        }

        return tmp.join('');
    },

    // Метод addChatLine добавляет строку чата на страницу
    addChatLine : function(args){
        var markup = simpleChat.render('chat', args);
        $('#chat').append(markup);
    },
    // Метод addChatLine добавляет строку чата на страницу
    addUserLine : function(args){
        return simpleChat.render('user', args);
    },

    // Данный метод запрашивает последнюю запись в чате
    // (начиная с offset), и добавляет ее на страницу.
    getChats : function(callback){
        get(
            '/chat/get-messages',
            {
                room: $('#room').val(),
                offset: simpleChat.data.offset
            },
            function(response) {
                if (response.success) {
                    for (var i = 0; i < response.data.length; i++) {
                        simpleChat.addChatLine(response.data[i]);
                    }

                    if (response.data.length) {
                        simpleChat.data.noActivity = 0;
                        simpleChat.data.offset = response.offset;
                    }
                    else {
                        // Если нет записей в чате, увеличиваем
                        // счетчик noActivity.
                        simpleChat.data.noActivity++;
                    }

                    // Устанавливаем таймаут для следующего запроса
                    // в зависимости активности чата:
                    var nextRequest = 1000;

                    // 2 секунды
                    if (simpleChat.data.noActivity > 3) {
                        nextRequest = 2000;
                    }

                    if (simpleChat.data.noActivity > 10) {
                        nextRequest = 5000;
                    }

                    // 15 секунд
                    if (simpleChat.data.noActivity > 20) {
                        nextRequest = 15000;
                    }

                    setTimeout(callback, nextRequest);
                }
            }
        );
    },
    getUsers : function (callback) {
        get('/chat/get-users',{},function(response) {
            if (response.success) {
                let listUser = "";
                for (var i = 0; i < response.data.length; i++) {
                    listUser+= simpleChat.addUserLine(response.data[i]);
                }
                $("#users-list").html(listUser);
                setTimeout(callback, 60000);
            }
         });
    }
};

// Формирование GET & POST:

post = function(action,data,callback){
    $.post(action,data,callback,'json');
};

get = function(action,data,callback){
    $.get(action,data,callback,'json');
};
