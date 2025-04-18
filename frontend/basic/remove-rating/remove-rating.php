<?php 

if( !class_exists('Sppcfw_Frontend_Remove_Rating')){
    class Sppcfw_Frontend_Remove_Rating{
        public function __construct(){
            // For traditional themes 
            add_action("woocommerce_single_product_summary",[$this, "sppcwf_remove_rating"],8); 

            // For block themes 
            add_filter('render_block', [$this, 'sppcfw_remove_reviews_in_block_themes'], 99, 2); 
        }

        public function sppcwf_remove_rating(){
            if($this->is_enabled()===1){
                remove_action("woocommerce_single_product_summary","woocommerce_template_single_rating", 10); 

            }
        }

        // Remove the reviews block for block themes
        public function sppcfw_remove_reviews_in_block_themes($block_content, $block) {
          if ($this -> is_enabled() === 1) {
            if (isset($block['blockName']) && $block['blockName'] === 'woocommerce/product-rating') {
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
                    if(isset($SPPCFW_INDIVIDUAL['remove_product_rating'])){
                        if($SPPCFW_INDIVIDUAL['remove_product_rating']==='on'){
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
                        
                        if(isset($sppcfw_cat['remove_product_rating'])){
                            if($sppcfw_cat['remove_product_rating']==='on'){
                                $enabled=1;
                            }
                        }
                    }

                    return $enabled;
                }

            }

            if(isset(SPPCFW_BASIC['remove_product_rating'])){
                if(SPPCFW_BASIC['remove_product_rating']==='on'){
                    $enabled=1;
                }
            }

            return $enabled;
        }
    }

    new Sppcfw_Frontend_Remove_Rating();
}

