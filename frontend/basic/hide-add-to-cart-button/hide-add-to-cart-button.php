<?php 

if( !class_exists('Sppcfw_Frontend_Hide_Add_To_Cart_Button')){
    class Sppcfw_Frontend_Hide_Add_To_Cart_Button{

        public function __construct(){
            // For traditional themes
            add_action("woocommerce_before_single_product_summary",[$this,"sppcfw_hide_add_to_cart_button"], 1);

            // For block themes
            add_action("render_block",[$this, "sppcfw_remove_add_to_card_form_in_block_themes"], 99, 2);
        }

        public function sppcfw_hide_add_to_cart_button(){
            if($this->is_enabled()===1){
                remove_action("woocommerce_single_product_summary","woocommerce_template_single_add_to_cart", 30);
            }
        }

        // Remove the add to card form block for block themes
        public function sppcfw_remove_add_to_card_form_in_block_themes($block_content, $block) {
            if ($this -> is_enabled() === 1) {
              if (isset($block['blockName']) && $block['blockName'] === 'woocommerce/add-to-cart-form') {
                  return ''; 
              }
            }
            return $block_content;
        }

        public function is_enabled(){
            $enabled=0;
            if(SPPCFW_PRO_ACTIVE){
                // check in product level
                if(sppcfw_if_product_based_customization_enabled()===1){
                    global $SPPCFW_INDIVIDUAL;
                    if(isset($SPPCFW_INDIVIDUAL['hide_add_to_cart_button'])){
                        if($SPPCFW_INDIVIDUAL['hide_add_to_cart_button']==='on'){
                           $enabled=1;
                        }else{
                           $enabled=0;
                        }                       
                    }
            
                    return $enabled;
                }

                // check in category level
                
                if(sppcfw_if_category_based_customization_enabled()===1){

                    $product_cat=sppcfw_get_product_category_id();
                    if($product_cat>0){
                        $sppcfw_cat = get_term_meta($product_cat, 'sppcfw_category_based_settings', true);
                        
                        if(isset($sppcfw_cat['hide_add_to_cart_button'])){
                            if($sppcfw_cat['hide_add_to_cart_button']==='on'){
                                $enabled=1;
                            }
                        }
                    }

                    return $enabled;
                }

            }

            if(isset(SPPCFW_BASIC['hide_add_to_cart_button'])){
                if(SPPCFW_BASIC['hide_add_to_cart_button']==='on'){
                    $enabled=1;
                }
            }

            return $enabled;
        }
    }

    new Sppcfw_Frontend_Hide_Add_To_Cart_Button();
}


