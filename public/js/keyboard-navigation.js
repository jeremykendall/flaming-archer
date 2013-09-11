$(function () {
    $(".photos").find(".photo").first().addClass('next');

    $(document).bind('keypress', 'return', function(event) {
        var photoLink = $('.current a');

        if (photoLink.length > 0) {
            location.href = photoLink.attr('href');
        }
    });

    $(document).bind('keydown', 'down', function() {
        var target = $(".next");
        scrollToPhoto(target);
    });

    $(document).bind('keypress', 'j', function(event) {
        var target = $(".next");
        scrollToPhoto(target);
    });

    $(document).bind('keydown', 'up', function() {
        var target = $(".prev");
        scrollToPhoto(target);
    });

    $(document).bind('keypress', 'k', function(event) {
        var target = $(".prev");
        scrollToPhoto(target);
    });

    $(document).bind('keydown', 'left', function() {
        var prev = $("#previous-day");
        if (prev.length) {
            location.href = prev.attr('href');
        }
    });

    $(document).bind('keypress', 'h', function() {
        var prev = $("#previous-day");
        if (prev.length) {
            location.href = prev.attr('href');
        }
    });

    $(document).bind('keydown', 'right', function() {
        var next = $("#next-day");
        if (next.length) {
            location.href = next.attr('href');
        }
    });

    $(document).bind('keypress', 'l', function() {
        var next = $("#next-day");
        if (next.length) {
            location.href = next.attr('href');
        }
    });
});

function scrollToPhoto(target)
{
    if (target.length > 0) {
        $.scrollTo(target, 150);
        swapClasses(target);
    }
}

function swapClasses(obj)
{
    var wasCurrent = $('.current');

    if (wasCurrent.length > 0 && wasCurrent.prev().length > 0) {
        wasCurrent.prev().removeClass('prev');
    }

    wasCurrent.removeClass('current');

    obj.removeClass('next').removeClass('prev').addClass('current');

    if (obj.next().length > 0) {
        obj.next().addClass('next');
    }

    if (obj.prev().length > 0) {
        obj.prev().addClass('prev');
    }
}

