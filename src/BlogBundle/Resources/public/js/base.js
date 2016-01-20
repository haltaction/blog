$(document).ready(function() {
    $('button#form_search').on('click', function(event){
        if ('' === $('.search-form input[name="s"]').val().trim()) {
            event.preventDefault();
        }
    });
});
