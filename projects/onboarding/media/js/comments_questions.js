CQ = {} // Comments & Questions object.

/*
 * Check if user have messages.
 * @return void.  
 */
CQ.checkMessages = function()
{
    var totalMessages        = window.totalMessages;
    var $messagesContainer   = $('.messages-container');
    var $noMessagesContainer = $('.alert-no-messages');
    
    //----- If user have messages then show messages container and hide alert container.
    if (totalMessages > 0){
        // show messages container
        $messagesContainer.show();
        // hide alert container
        $noMessagesContainer.hide();
    }
    //----- If user don't have messages then hide messages container and show alert container.
    else {
        // hide messages container
        $messagesContainer.hide();
        // show alert container
        $noMessagesContainer.show();
    }
}

$(document).ready(function(){
    // Init checkMessages function.
    CQ.checkMessages();
});