<?php 

if( !class_exists("Sppcfw_Frontend_Hide_Price")){
    class Sppcfw_Frontend_Hide_Price{
        public function __construct(){
            // For traditional themes
            add_action("woocommerce_before_single_product_summary",[$this, "sppcfw_hide_price_section"], 1);

            // For block themes
            add_action("render_block",[$this, "sppcfw_remove_product_price_in_block_themes"], 99, 2);
        }
    
        public function sppcfw_hide_price_section(){
            if($this->is_enabled()===1){
             remove_action("woocommerce_single_product_summary","woocommerce_template_single_price", 10);
            }
        }

        // Remove the product price block for block themes
        public function sppcfw_remove_product_price_in_block_themes($block_content, $block) {
            if ($this -> is_enabled() === 1) {
              if (isset($block['blockName']) && $block['blockName'] === 'woocommerce/product-price') {
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
                    if(isset($SPPCFW_INDIVIDUAL['hide_product_price'])){
                        if($SPPCFW_INDIVIDUAL['hide_product_price']==='on'){
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
                        
                        if(isset($sppcfw_cat['hide_product_price'])){
                            if($sppcfw_cat['hide_product_price']==='on'){
                                $enabled=1;
                            }
                        }
                    }

                    return $enabled;
                }

            }

            if(isset(SPPCFW_BASIC['hide_product_price'])){
                if(SPPCFW_BASIC['hide_product_price']==='on'){
                    $enabled=1;
                }
            }

            return $enabled;
        }
  }

  new Sppcfw_Frontend_Hide_Price();
}

