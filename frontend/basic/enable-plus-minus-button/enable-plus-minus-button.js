/*sp plus minus button section*/


jQuery(document).on( 'click', 'button.sppcfw_plus_button, button.sppcfw_minus_button', function() {

   
    var qty = jQuery( this ).parent( '.quantity' ).find( '.qty' );
    var val = parseFloat(qty.val());
    var max = parseFloat(qty.attr( 'max' ));
    var min = parseFloat(qty.attr( 'min' ));
    var step = parseFloat(qty.attr( 'step' ));

    if ( jQuery( this ).is( '.sppcfw_plus_button' ) ) {
       if ( max && ( max <= val ) ) {
          qty.val( max ).trigger('change');
       } else {
          qty.val( val + step ).trigger('change');
       }
    } else {
       if ( min && ( min >= val ) ) {
          qty.val( min ).trigger('change');
       } else if ( val > step ) {
          qty.val( val - step ).trigger('change');
       }
    }

 });
   
/*sp plus minus button section end*/