$(document).ready(function(){
    // $('.ui.dropdown')
    //     .dropdown('restore defaults')
    // ;

    // $('.select2.dropdown').select2({
    //
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
        },
        history: true,
        historyType: 'hash'
    });

    $('.tabular.submenu .item').tab({
        onVisible: function() {
            $('.image img').visibility('refresh');
        },
        history: true,
        historyType: 'hash'
    });

    $('.message .close')
        .on('click', function() {
            $(this)
                .closest('.message')
                .transition('fade')
            ;
        })
    ;

    $('.ui.modal')
        .modal('attach events', '.ui.open-modal.button', 'show')
    ;

    // create sidebar and attach to menu open
    $('.ui.sidebar')
        .sidebar('attach events', '.toc.item')
    ;
});