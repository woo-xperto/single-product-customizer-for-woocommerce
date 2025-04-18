

    /* Variation swithcher Frontend section */

    jQuery(document).ready(function($){

        let webcfwc_variation_btn_attributes = $("body").find("table.variations tbody tr td button.webfwc_variation_button");
        
        $(webcfwc_variation_btn_attributes).on("click",function(){
            let webcfwc_variation_button_val = $(this).attr("data-val");

            let webcfwc_varation_attr = $(this).data("attr");

            $('#'+webcfwc_varation_attr+'').val(webcfwc_variation_button_val);
            
            $('#'+webcfwc_varation_attr+'').trigger('change');

        });

        $('.webfwc_variation_button.color').each(function() {
            var bgColor = $(this).data('bg-color');  
            
            if (bgColor) {
                $(this).css('background-color', bgColor);
            }
        });


        /*disable vaiation pear if not math*/
        $(document).change(".vairation_select",function(){
        
            $(".vairation_select").each(function(){
                let selected_val=$(this).val();
                
                let parent = $(this).parent();
                $(parent).find("button.webfwc_variation_button.selected").removeClass("selected");
                if(selected_val){               
                $(parent).find("button.webfwc_variation_button[data-val='"+selected_val+"']").addClass("selected");
                }

                let all_variation_options =  $(this).parent().find( "option");
                let variation_all_buttons = $(this).parent().find(".webfwc_variation_button");
                let option_values=[];
                $.each(all_variation_options, function(){
                let option_val=$(this).attr("value");
                if(option_val) option_values.push(option_val);
                });

                $.each(variation_all_buttons,function(){
                let btn_val=$(this).attr("data-val");
                if(option_values.includes(btn_val)){
                $(this).removeClass('webcfwc_btn_disable');
                }else{
                $(this).addClass('webcfwc_btn_disable');
                }
                });
            });
        });
        /*disable vaiation pear if not math*/


    });

   /* Variation swithcher Frontend section end*/





