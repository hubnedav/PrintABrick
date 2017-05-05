$(document).ready(function () {
   $('.ajax-load').each(function () {
       $self = $(this);

       $.ajax({
           dataType: "json",
           url: $self.data('src'),
           context: $self
       }).done(function(data) {
           $(this).html(data);
       });
   })
});

$(document).ajaxComplete(function () {
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
});