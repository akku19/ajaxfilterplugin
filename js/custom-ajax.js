jQuery(document).ready(function($) {
    $('.category-checkbox').on('change', function() {
        var selectedCategories = [];
        $('.category-checkbox:checked').each(function() {
            selectedCategories.push($(this).val());
        });

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'load_products',
                cat_ids: selectedCategories
            },
            success: function(response) {
                $('#product-list').html(response);
            },
            error: function() {
                $('#product-list').html('There was an error loading the products.');
            }
        });
    });
});
