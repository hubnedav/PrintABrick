$(document).ready(function () {
    $('.ajax-load').each(function () {
        let self = $(this);

        $.ajax({
            dataType: "json",
            url: self.data('src'),
            context: self
        }).done(function(data) {
            $(this).html(data);

            self.find('.image.load img')
                .visibility({
                    type       : 'image',
                    transition : 'fade in',
                })
            ;

            self.find('.ui.rating')
                .rating("disable")
            ;
        });
    })
});