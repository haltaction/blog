$(document).ready(function() {
    var buttonShow = $('.show-more');

    isShow(buttonShow, isNextPage);

    buttonShow.on('click', function(e){
        var page = buttonShow.attr('data-page'),
            getParams = window.location.search;
        e.preventDefault();

        $.ajax(ajaxurl + '/' + ++page + getParams).done(function(data){
            var html = data.html,
                isNext = data.isNextPage;

            $('.comments-list').append(html);
            buttonShow.attr('data-page', page);
            isShow(buttonShow, isNext);
        });
    });

    function isShow(element, isShow) {
        if (!isShow) {
            element.addClass('hidden');
        }
        return element;
    }
});
