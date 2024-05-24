alert("yes")
jQuery(document).ready(function($) {
    $('.category-checkbox').on('change', function() {
        var cat_ids = [];
        $('.category-checkbox:checked').each(function() {
            cat_ids.push($(this).val());
        });

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'load_products',
                cat_ids: cat_ids
            },
            success: function(response) {
                $('#product-list').html(response);
            }
        });
    });
  
    $(document).on('click', '.add-to-cart-btn', function() {
        console.log('yes');
        var product_id = $(this).data('product_id');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: product_id
            },
            success: function(response) {
                if (response.success) {
                    alert('Product added to cart');
                } else {
                    alert('Failed to add product to cart');
                }
            }
        });
    });
});
