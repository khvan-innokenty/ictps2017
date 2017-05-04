(function( $, document, window ) {
    var $jsSelectDay = $(".js-select-day"),
        $jsDaySchedule = $(".js-day-schedule"),
        $navigation = $("#navigation"),
        $hamburger = $(".hamburger");

    $jsSelectDay.click(function(e) {
        var $this = $(this),
            href = $this.prop('href').split('#')[1];

        e.preventDefault();

        $jsSelectDay.removeClass('active');
        $this.addClass('active');
        $jsDaySchedule.addClass('hidden');
        $('#' + href).removeClass('hidden');
    });

    $hamburger.click(function(){
        $hamburger.toggleClass('active');
        $navigation.toggleClass('active');
    });

    $navigation.find('a').click(function(){
        $hamburger.removeClass('active');
        $navigation.removeClass('active');
    });
})( jQuery, document, window );