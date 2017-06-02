$(document).ready(function() {
    $('.number-range').each(function () {
        var id = $(this).attr('id');
        var $slider = $(this).children('.slider');
        var $from = $(this).children('input[id*="from"]');
        var $to = $(this).children('input[id*="to"]');

        $slider.slider({
            range: true,
            min: parseInt($slider.attr('min')),
            max: parseInt($slider.attr('max')),
            step: $slider.attr('step'),
            values: [$from.val(), $to.val()],
            slide: function (event, ui) {
                $from.val(ui.values[0]);
                $to.val(ui.values[1]);

                $("#" + id + "_value").text(ui.values[0] + " - " + ui.values[1]);
            }
        }).slider("pips", {}).draggable();

        $("#" + id + "_value").text($slider.slider("values", 0) + ' - ' + $slider.slider("values", 1))
    });
});