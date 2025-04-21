<?php 

if( !class_exists("Sppcfw_Variation_Switcher")){
    
    class Sppcfw_Variation_Switcher{

        public function __construct(){

            add_action("wp_enqueue_scripts",[$this, "sppcfw_variation_switcher_assets"]);

            add_filter("woocommerce_dropdown_variation_attribute_options_html",[$this,"sppcfw_display_variation_switcher"],10,2);
        }


        public function sppcfw_variation_switcher_assets(){
            
            if($this->is_enabled()===1){
                
                wp_enqueue_script(
                    'sppcfw-variation-switcher-js',
                    plugin_dir_url(__FILE__).'variation-switcher.js',
                    array( 'jquery'),
                    true,
                    SPPCFW_VERION
                );

                wp_enqueue_style(
                    'variation-switcher-css',
                    plugin_dir_url(__FILE__).'variation-switcher.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );

                wp_enqueue_style(
                    'frontend-fontawesome-css',
                    plugin_dir_url(__FILE__).'./css/fontawesome.min.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );
                
    
                wp_enqueue_style(
                    'backend-iconsmind-css',
                    plugin_dir_url(__FILE__).'./css/iconsmind.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );
    
                wp_enqueue_style(
                    'backend-linea-css',
                    plugin_dir_url(__FILE__).'./css/fonts/svg/font/style.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );
                wp_enqueue_style(
                    'backend-linecon-css',
                    plugin_dir_url(__FILE__).'./css/linecon.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );
                wp_enqueue_style(
                    'backend-steadysets-css',
                    plugin_dir_url(__FILE__).'./css/steadysets.css',
                    null,
                    SPPCFW_VERION,
                    'all'
                );         
            }
                    
        }

        public function is_enabled(){
            $enabled=0;
            if(SPPCFW_PRO_ACTIVE){
                if(isset(SPPCFW_ADVANCED['enable_variation_switcher'])){
                    if(SPPCFW_ADVANCED['enable_variation_switcher']==='on'){
                        $enabled=1;
                    }
                }
            }

            return $enabled;
        }

        public function sppcfw_get_attribute_type($attribute_name){

            global $wpdb;
            
            $table_name = $wpdb->prefix."woocommerce_attribute_taxonomies";
            $attribute_type='';
            // phpcs:ignore
            $result = $wpdb->get_results($wpdb->prepare("SELECT attribute_type FROM $table_name where attribute_name=%s", $attribute_name ));

            if($result){
                if(count($result)>0){
                    if(isset($result[0]->attribute_type)){
                        $attribute_type=$result[0]->attribute_type;
                    }
                }
            }
            return $attribute_type;
        }

        public function sppcfw_get_attribute_type_meta_value($slug,$taxonomy){
            //echo $slug;
            $term=get_term_by('slug',$slug,$taxonomy,'ARRAY_A');
            //print_r($term);
            $value='';
            if(is_array($term)){
                if(isset($term["term_id"])){      
                    $value=get_term_meta($term["term_id"],'webcfwc_variation_meta',true); 
                }
            }
        
            return $value;
        }

        public function sppcfw_display_variation_switcher( $html, $args ){
            if($this->is_enabled()===1){
                $options = $args['options'];
            
                $attribute = $args['attribute']; // pa_size, pa_color,pa_....
                
            
                $attribute_name=ltrim($attribute, 'pa_');
                //$attribute_name=substr($attribute, 3);
            
                $attributes_type =  $this->sppcfw_get_attribute_type($attribute_name);
            
                $attribute_name = 'attribute_'.$attribute ;
                $select='<select id="'.$attribute.'" class="vairation_select" data-attribute_name="'.$attribute_name.'" name="'.$attribute_name.'">';
                $select.='<option value="">__("Chose one", "single-product-customizer")</option>';
                $button='';
            
            
                foreach($options as $option){
            
                    $select.='<option value="'.$option.'">'.$option.'</option>';
                    $option_meta=$this->sppcfw_get_attribute_type_meta_value($option,$attribute);
            
                    switch ($attributes_type) {
                        case 'color':
                            $button.='<button data-val="'.$option.'" class="webfwc_variation_button color" data-bg-color="'.$option_meta.'" type="button" data-attr="'.$attribute.'"></button>';
                            break;
                        
                        case 'icon':
                            $button.='<button data-val="'.$option.'" class="webfwc_variation_button icon" type="button" data-attr="'.$attribute.'">
                                <i class="'.$option_meta.'"></i>
                            </button>';
                            break;
                        case 'button':
                            $button.='<button data-val="'.$option.'" class="webfwc_variation_button button" type="button" data-attr="'.$attribute.'">'.$option.'</button>';
                            break;
                        case 'image':
                            $img='';
                            if($option_meta>0){
                                $img=wp_get_attachment_image($option_meta,'thumbnail');
                            }
                            $button.='<button data-val="'.$option.'" class="webfwc_variation_button image" type="button" data-attr="'.$attribute.'">
                            '.$img.'
                            </button>';
                            break;

                            default :
                            $button.='<button data-val="'.$option.'" class="webfwc_variation_button button" type="button" data-attr="'.$attribute.'">'.$option.'</button>';
                    }
            
                    
                }
            
                $select.='</select>';
            
                return '<div class="cu_button_el">'.$select.$button.'</div>';
            }

            return $html;
        }
    }

    new Sppcfw_Variation_Switcher();
}
