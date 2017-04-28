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