jQuery(window).load(function() {
	jQuery("ul.woof_list_radio li a.woof_radio_term_reset img").attr('src', 'http://navro.com.ua/wp-content/themes/new-navro-child/images/delete.png');
	jQuery(".woof_products_top_panel li span.woof_remove_ppi").css('background-image','url(http://navro.com.ua/wp-content/themes/new-navro-child/images/delete.png)');
});

//jQuery("ul.woof_list_radio li a.woof_radio_term_reset img").one("load", function() {}).attr("src", 'http://dev.navro.com.ua/wp-content/themes/new-navro-child/images/delete.png');

/*
jQuery(document).ready(function() {
	var shop_body=(jQuery(".shop_body").parent(".et_pb_column").height());
	var shop_filter=(jQuery(".shop_filter").parent(".et_pb_column").height());
	alert(shop_body+'>'+shop_filter);
	if (shop_body>shop_filter){
		jQuery(".shop_filter").parent(".et_pb_column").height(shop_body);
	}
});
*/


$(document).ready(function() {
	$('select').addClass('selected_parm');
	
	$( ".selected_parm" ).change(function() {
		$(this).addClass('black_selector');
	});
}); 

jQuery(document).ready(function() {
	var pathname=(window.location.pathname);
	//alert(pathname);
	if (pathname.indexOf("cart")>=0){
		jQuery('.woocommerce-message').css('display','none');
	}
	if (jQuery('ul.woocommerce-error').length>0){
		jQuery( ".variations select" ).each(function() {
			if (!jQuery(this).val()) {
				//alert(jQuery(this).attr('id'));
				jQuery(this).css('border-color', '#FF0000');
			}
		})
			if (!jQuery(this).val()) {
				jQuery(".quantity_select select.qty").css('border-color', '#FF0000');
			}
	}
	
	var count=jQuery(".variations select").size();
	var select_count=0;
	jQuery("form.cart").on("change", ".variations select", function() {
		jQuery( ".variations select" ).each(function() {
			if (jQuery(this).val()) {
				++select_count;
			}
			if (select_count==count){
				//alert('All is select');
				jQuery("select.qty").val('1');
				//jQuery("select.qty").val(jQuery("select.qty option:first").val());
			}
		})
	})

	jQuery('button.mixis_js_check').click(function() {
		if(!jQuery( ".quantity_select select" ).val()){
			jQuery(".quantity_select select").css('border-color', '#FF0000');
			jQuery( 'nav.woocommerce-breadcrumb').after('<ul class="woocommerce-error"><li>Please choose product options…</li></ul>');
			event.preventDefault();
		}
	})

});
