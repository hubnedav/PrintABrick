$(document).bind('ready ajaxComplete', function(){
    $('.ui.dropdown')
        .dropdown()
    ;

    $('.image.load img')
        .visibility({
            type       : 'image',
            transition : 'fade in',
            duration   : 1000
        })
    ;

    $('.ui.rating')
        .rating("disable")
    ;

    $('.tabular.menu .item').tab();

    $('.message .close')
        .on('click', function() {
            $(this)
                .closest('.message')
                .transition('fade')
            ;
        })
    ;

    $('.item-info .download')
        .popup({
            popup : $('.download.popup'),
            on: 'click',
            inline: true
        })
    ;
});