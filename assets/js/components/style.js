import 'select2';
import 'select2/dist/css/select2.css';

$(document).ready(function(){
    $('.image.load img')
        .visibility({
            type       : 'image',
            transition : 'fade in',
        })
    ;

    $('.select2').select2();

    $('.ui.rating')
        .rating("disable")
    ;

    $('.tabular.menu .item, .tabular.submenu .item').tab({
        onVisible: function(data) {
            $(this).find('.image.load img').visibility('refresh');
        },
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

    // save last selected inventory tab to session
    $('.setTab').click(function() {
        let tab = $(this).attr('data-tab');

        $.ajax({
            type: 'POST',
            url: routes.set_tab.replace("PLACEHOLDER", tab),
            async: true,
        });
    });

    $('.ui.search')
        .search({
            type: 'category',
            apiSettings: {
                action: 'search',
                url: routes.search_autocomplete+'?query={query}',
            },
            minCharacters: 3,
            fields: {
                title: 'name',
                description: 'id',
                url: 'url',
                image: 'img'
            }
        });
});