// Quick Chat 4.10 - load
var quick_chat = jQuery.extend(quick_chat || {}, {
    script_suffix: (quick_chat.debug_mode == 1) ? '.dev' : '',
    get_script: function(url, callback, options) {
        options = jQuery.extend(options || {}, {
            crossDomain: (quick_chat.debug_mode == 1)? true : false,
            dataType: "script",
            cache: true,
            success: callback,
            url: url
        });

        return jQuery.ajax(options);
    },
    load: function(){
        if(jQuery('div.quick-chat-container').length != 0 || (jQuery.cookie('quick_chat_private_current') && jQuery.cookie('quick_chat_private_current') != '{}'))
            quick_chat.get_script(quick_chat.url+'js/quick-chat-init'+quick_chat.script_suffix+'.js?'+quick_chat.version);
    }
});

if (jQuery.browser.webkit) {
    // Webkit bug workaround: http://code.google.com/p/chromium/issues/detail?id=41726
    jQuery(window).load(quick_chat.load());
}else{
    jQuery(document).ready(quick_chat.load());
}