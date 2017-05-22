$(document).ready(function(){
    // $('.ui.dropdown')
    //     .dropdown('restore defaults')
    // ;

    // $('.select2.dropdown').select2({
    //     placeholder: 'Select theme',
    //     allowClear: true
    // });

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

    $('.tabular.menu .item').tab({
        onVisible: function() {
            $('.image img').visibility('refresh');
        }
    });

    $('.tabular.submenu .item').tab({
        onVisible: function() {
            $('.image img').visibility('refresh');
        }
    });


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

    $('.ui.modal')
        .modal('attach events', '.ui.open-modal.button', 'show')
    ;
});