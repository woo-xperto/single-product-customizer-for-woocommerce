<?php 

if( !class_exists("Sppcfw_Frontend_Hide_Short_Description")){
    class Sppcfw_Frontend_Hide_Short_Description{
        public function __construct(){
            // For traditional themes
            add_action("woocommerce_before_single_product_summary",[$this,"sppcfw_hide_short_description"], 1);


            // Add CSS for fallback
            add_action('wp_enqueue_scripts', [$this, 'sppcfw_hide_woocommerce_short_description_css']);
        }
        
        public function sppcfw_hide_short_description(){
            if($this->is_enabled()===1){
                remove_action("woocommerce_single_product_summary","woocommerce_template_single_excerpt", 20);
            }
        }

        // Add CSS to hide the block as a fallback
        public function sppcfw_hide_woocommerce_short_description_css() {
            if($this->is_enabled()===1){
                wp_add_inline_style('wp-block-library', '
                    .wp-block-post-excerpt {
                        display: none !important;
                    }
                ');
            }
        }

        public function is_enabled(){
            $enabled=0;
            if(SPPCFW_PRO_ACTIVE){
                // check in product level
                if(sppcfw_if_product_based_customization_enabled()===1){
                    global $SPPCFW_INDIVIDUAL;
                    if(isset($SPPCFW_INDIVIDUAL['hide_short_description'])){
                        if($SPPCFW_INDIVIDUAL['hide_short_description']==='on'){
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
                        
                        if(isset($sppcfw_cat['hide_short_description'])){
                            if($sppcfw_cat['hide_short_description']==='on'){
                                $enabled=1;
                            }
                        }
                    }

                    return $enabled;
                }

            }

            if(isset(SPPCFW_BASIC['hide_short_description'])){
                if(SPPCFW_BASIC['hide_short_description']==='on'){
                    $enabled=1;
                }
            }

            return $enabled;
        }
    }

    new Sppcfw_Frontend_Hide_Short_Description();
}



