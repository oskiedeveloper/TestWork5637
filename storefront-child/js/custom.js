jQuery(document).ready(function($) {
    $('#search-button').on('click', function() {
        var searchTerm = $('#search-input').val();
        console.log(searchTerm);
        $.ajax({
            url: ajax_object.ajaxurl, // WordPress provides this variable for AJAX URLs
            type: 'POST',
            data: {
                action: 'ajax_search',
                search: searchTerm
            },
            success: function(response) {
                // Update the table with the search results
                $('#city-table').html(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
        
    });
});