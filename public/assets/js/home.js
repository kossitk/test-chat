$(window).ready(function() {
    let onlineArea = $('#onlineUsers');
    let messageContainer = $('#messageContainer');
    let messageTemplate = $('#messageTemplate');
    let activeChat = null;
    let connecteUser = $('#connectedUserBox').attr('data-user');
    let previousMessageLink = $('#loadPreviousMessages');
    let loadingMessages = false;
    let hasOldMessages = true;


    $('#viewOnlineUsers').click(function(e) {
        e.preventDefault();
        let link = $(this).attr('href');
        $.ajax({
            url:     link,
            method:  'GET',
            type:    'html',
            success: function (response) {
                onlineArea.html(response);
                onlineArea.removeClass('d-none');
            },
            error:   function () {
                console.log('Error for online users!');
            },
        });
    });

    $('.discussion').click(function () {
        let $this = $(this);
        if (!$this.hasClass('active-chat')){
            activeChat = $this.attr('data-id');
            messageContainer.find('.message').remove();
            $this.siblings().removeClass('active-chat');
            $this.addClass('active-chat');
            hasOldMessages = true;
            previousMessageLink.removeClass('d-none');
            getMessages();
        }
    });

    previousMessageLink.click(function () {
        getPreviousMessages();
    });

    function getMessages() {
        loadingMessages = true;
        var lastMessage = messageContainer.find('.message:last-child').attr('data-id');
        $.ajax({
            url:     '/messages',
            method:  'POST',
            type:    'html',
            data: {
                'chat' : activeChat,
                'lastMessage': lastMessage,
                'recent' : true,
            },
            success: function (response) {
                console.log(response);
                if (response.success == true){
                    addMessage(response.messages, true);
                    loadingMessages = false;
                }
            },
            error:   function () {
                console.log('Error for online users!');
            },
        });
    }

    function getUnreadCounter() {
        $.ajax({
            url:     '/unread-counter',
            method:  'GET',
            type:    'html',
            success: function (response) {
                $.each(response, function (i, el) {
                    let chat = $('#chat_'+el.chat);
                    if (chat.length > 0){
                        let badge = chat.find('.unread .badge');
                        badge.text(el.unread);
                        if (el.unread > 0){
                            badge.removeClass('d-none');
                        }
                        else {
                            badge.addClass('d-none');
                        }
                    }
                });
        },
            error:   function () {
                console.log('Error for online users!');
            },
        });
    }

    function getPreviousMessages() {
        var first = messageContainer.find('.message:first-of-type').attr('data-id');
        $.ajax({
            url:     '/messages-older',
            method:  'POST',
            type:    'html',
            data: {
                'chat' : activeChat,
                'first': first,
            },
            success: function (response) {
                if (response.success == true){
                    addMessage(response.messages, false);
                }
            },
            error:   function () {
                console.log('Error for online users!');
            },
        });
    }

    function addMessage(messages, recent)
    {
        if(false === recent && true === hasOldMessages && messages.length == 0){
            hasOldMessages = false;
            previousMessageLink.addClass('d-none');
        }

        if (hasOldMessages){
            previousMessageLink.removeClass('d-none');
        }
        $.each(messages, function (i, el) {
            let messageBox = $(messageTemplate.html());
            if (recent){
                messageContainer.append(messageBox);
            }
            else{
                messageContainer.prepend(messageBox);
            }
            messageBox.find('.user').text(el.pseudo);
            messageBox.find('.date').text(el.created_on);
            messageBox.find('.message-content').text(el.content);
            messageBox.attr('data-id', el.uuid);

            if (el.user_uuid == connecteUser) {
                messageBox.addClass('align-self-end custom');
            }
        });
        if (!recent){
            previousMessageLink.detach();
            messageContainer.prepend(previousMessageLink)
        }
    }

    $('#formAddMessage').submit(function(e){
        e.preventDefault();
        if (!activeChat){
            alert('Please select a chat on the right panel');
            return false;
        }
        var message = $('#addMessageTextarea');
        var lastMessage = messageContainer.find('.message:last-child').attr('data-id');
        if (message.val().length > 0){
            $.ajax({
                url:     '/add-message',
                method:  'POST',
                type:    'html',
                data: {
                    'chat' : activeChat,
                    'message': message.val(),
                    'lastMessage': lastMessage,
                },
                success: function (response) {
                    if(response.success == true){
                        message.val('');
                        addMessage(response.messages, true);
                    }
                    else{
                        alert('An error occur during saving message');
                    }
                },
                error:   function () {
                    console.log('Error for online users!');
                },
            });
        }
        else{
            alert('Please add a message');
        }
    });

    // Make pseudo realtime chat
    setInterval(function () {
        if (false == loadingMessages ){
            if (activeChat){
                getMessages();
            }
            getUnreadCounter();
        }
    }, 4000);
});
