<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SPPCFW_backend_ui' ) ) :

	class SPPCFW_backend_ui {

		/**
		 * Sections array.
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $sections_array = array();

		/**
		 * Fields array.
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $fields_array = array();

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
			// Hook it up.
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'sppcfw_register_admin_menu' ) );

		}

		/**
		 * Set Sections.
		 *
		 * @param array $sections
		 * @since 1.0.0
		 */
		public function set_sections( $sections ) {
			// Bail if not array.
			if ( ! is_array( $sections ) ) {
				return false;
			}

			// Assign to the sections array.
			$this->sections_array = $sections;

			return $this;
		}


		/**
		 * Add a single section.
		 *
		 * @param array $section
		 * @since 1.0.0
		 */
		public function add_section( $section ) {
            //echo $section['id'];
			// Bail if not array.
			if ( ! is_array( $section ) ) {
				return false;
			}

			// Assign the section to sections array.
			$this->sections_array[] = $section;

            //print_r($this->sections_array);
			return $this;
		}


		/**
		 * Set Fields.
		 *
		 * @since 1.0.0
		 */
		public function set_fields( $fields ) {
			// Bail if not array.
			if ( ! is_array( $fields ) ) {
				return false;
			}

			// Assign the fields.
			$this->fields_array = $fields;

			return $this;
		}



		/**
		 * Add a single field.
		 *
		 * @since 1.0.0
		 */
		public function add_field( $section, $field_array ) {
			// Set the defaults
			$defaults = array(
				'id'   => '',
				'name' => '',
				'desc' => '',
				'type' => 'text',
                'class'=>''
			);

			// Combine the defaults with user's arguements.
			$arg = wp_parse_args( $field_array, $defaults );
            //print_r($arg);
			// Each field is an array named against its section.
			$this->fields_array[ $section ][] = $arg;

			return $this;
		}


		/**
		 * Initialize API.
		 *
		 * Initializes and registers the settings sections and fields.
		 * Usually this should be called at `admin_init` hook.
		 *
		 * @since  1.0.0
		 */
		function admin_init() {
			/**
			 * Register the sections.
			 *
			 * Sections array is like this:
			 *
			 * $sections_array = array (
			 *   $section_array,
			 *   $section_array,
			 *   $section_array,
			 * );
			 *
			 * Section array is like this:
			 *
			 * $section_array = array (
			 *   'id'    => 'section_id',
			 *   'title' => 'Section Title'
			 * );
			 *
			 * @since 1.0.0
			 */
            // print_r($this->sections_array);
			foreach ( $this->sections_array as $section ) {
				if ( false == get_option( $section['id'] ) ) {
					// Add a new field as section ID.
					add_option( $section['id'] );
				}

				// Deals with sections description.
				if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
					// Build HTML.
					$section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';

					// Create the callback for description.
					$callback = function() use ( $section ) {
						echo esc_html(str_replace( '"', '\"', $section['desc'] ));
					};

				} elseif ( isset( $section['callback'] ) ) {
					$callback = $section['callback'];
				} else {
					$callback = null;
				}

				/**
				 * Add a new section to a settings page.
				 *
				 * @param string $id
				 * @param string $title
				 * @param callable $callback
				 * @param string $page | Page is same as section ID.
				 * @since 1.0.0
				 */
				add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
			} // foreach ended.

			/**
			 * Register settings fields.
			 *
			 * Fields array is like this:
			 *
			 * $fields_array = array (
			 *   $section => $field_array,
			 *   $section => $field_array,
			 *   $section => $field_array,
			 * );
			 *
			 *
			 * Field array is like this:
			 *
			 * $field_array = array (
			 *   'id'   => 'id',
			 *   'name' => 'Name',
			 *   'type' => 'text',
			 * );
			 *
			 * @since 1.0.0
			 */
			foreach ( $this->fields_array as $section => $field_array ) {
				foreach ( $field_array as $field ) {
					// ID.
					$id = isset( $field['id'] ) ? $field['id'] : false;
					$class = isset( $field['class'] ) ? $field['class'] : '';

					// Type.
					$type = isset( $field['type'] ) ? $field['type'] : 'text';

					// Name.
					$name = isset( $field['name'] ) ? $field['name'] : __("No Name Added", "single-product-customizer");

					// Label for.
					$label_for = "{$section}[{$field['id']}]";

					// Description.
					$description = isset( $field['desc'] ) ? $field['desc'] : '';

					// Size.
					$size = isset( $field['size'] ) ? $field['size'] : null;

					// Options.
					$options = isset( $field['options'] ) ? $field['options'] : '';

					// Standard default value.
					$default = isset( $field['default'] ) ? $field['default'] : '';

					// Standard default placeholder.
					$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';

					// Sanitize Callback.
					$sanitize_callback = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '';

					$args = array(
						'id'                => $id,
						'class'                => $class,
						'type'              => $type,
						'name'              => $name,
						'label_for'         => $label_for,
						'desc'              => $description,
						'section'           => $section,
						'size'              => $size,
						'options'           => $options,
						'std'               => $default,
						'placeholder'       => $placeholder,
						'sanitize_callback' => $sanitize_callback,
					);

					/**
					 * Add a new field to a section of a settings page.
					 *
					 * @param string   $id
					 * @param string   $title
					 * @param callable $callback
					 * @param string   $page
					 * @param string   $section = 'default'
					 * @param array    $args = array()
					 * @since 1.0.0
					 */

					// @param string 	$id
					$field_id = $section . '[' . $field['id'] . ']';

					add_settings_field(
						$field_id,
						$name,
						array( $this, 'callback_' . $type ),
						$section,
						$section,
						$args
					);
				} // foreach ended.
			} // foreach ended.

			// Creates our settings in the fields table.
			foreach ( $this->sections_array as $section ) {
                
				/**
				 * Registers a setting and its sanitization callback.
				 *
				 * @param string $field_group   | A settings group name.
				 * @param string $field_name    | The name of an option to sanitize and save.
				 * @param callable  $sanitize_callback = ''
				 * @since 1.0.0
				 */
				register_setting( $section['id'], $section['id'], array( $this, 'sanitize_fields' ) );
                
			} // foreach ended.

		} // admin_init() ended.


		/**
		 * Sanitize callback for Settings API fields.
		 *
		 * @since 1.0.0
		 */
		public function sanitize_fields( $fields ) {
			foreach ( $fields as $field_slug => $field_value ) {
				$sanitize_callback = $this->get_sanitize_callback( $field_slug );

				// If callback is set, call it.
				if ( $sanitize_callback ) {
					$fields[ $field_slug ] = call_user_func( $sanitize_callback, $field_value );
					continue;
				}
			}

			return $fields;
		}


		/**
		 * Get sanitization callback for given option slug
		 *
		 * @param string $slug option slug.
		 * @return mixed string | bool false
		 * @since  1.0.0
		 */
		function get_sanitize_callback( $slug = '' ) {
			if ( empty( $slug ) ) {
				return false;
			}

			// Iterate over registered fields and see if we can find proper callback.
			foreach ( $this->fields_array as $section => $field_array ) {
				foreach ( $field_array as $field ) {
					if ( $field['name'] != $slug ) {
						continue;
					}

					// Return the callback name.
					return isset( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : false;
				}
			}

			return false;
		}


		/**
		 * Get field description for display
		 *
		 * @param array $args settings field args
		 */
		public function get_field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}


		/**
		 * Displays a title field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_title( $args ) {
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			if ( '' !== $args['name'] ) {
				$name = $args['name'];
			} else {
			};
			$type = isset( $args['type'] ) ? $args['type'] : __("Title", "single-product-customizer");

			$html = '';
			echo esc_html($html);
		}


		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_text( $args ) {
			// Retrieve the value from the database or set the default
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		
			// Determine input size and type
			$size = isset( $args['size'] ) ? esc_attr( $args['size'] ) : 'regular';
			$type = isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';

			$html  = sprintf(
				'<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s" placeholder="%6$s" />',
				esc_attr( $type ),
				esc_attr( $size ),
				esc_attr( $args['section'] ),
				esc_attr( $args['id'] ),
				esc_attr( $value ),
				esc_attr( $args['placeholder'] )
			);
		
			$html .= $this->get_field_description( $args );
		
			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'input' => array(
					'type' => array(),
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
					'placeholder' => array(),
				),
				'p' => array(
					'class' => array(),
				),
			);
		
			// Sanitize and output the HTML
			echo wp_kses( $html, $allowed_html );
		}
		

		/**
		 * Displays a url field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_url( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a number field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_number( $args ) {
			$this->callback_text( $args );
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_checkbox( $args ) {
			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
		
			$html = '<fieldset>';
			$html .= sprintf( '<label for="wxspc-%1$s[%2$s]">', $args['section'], $args['id'] );
			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
		
			// Conditional Replacement for 'enable_min_max_qty' field
			if ($args['type'] === 'checkbox' && ($args['id'] === 'enable_customizer_for_category' || $args['id'] === 'enable_customizer_for_product' || $args['id'] === 'enable_ajax_add_to_cart' || $args['id'] === 'enable_min_max_qty' || $args['id'] === 'enable_custom_tab' || $args['id'] === 'enable_additional_content' || $args['id'] === 'enable_change_tab_default_label' || $args['id'] === 'related_product_categories')) {
				// Replace the input field with a notice for the PRO version
				$html .= sprintf('<p class="pro-notice">%s</p>', __('PRO Feature', 'single-product-customizer'));
			} else {
				// Render the checkbox if not the 'enable_min_max_qty' field
				$html .= sprintf(
					'<input type="checkbox" class="checkbox %1$s" id="wxspc-%2$s[%3$s]" name="%2$s[%3$s]" value="on" %4$s />',
					esc_attr( $args['class'] ),
					esc_attr( $args['section'] ),
					esc_attr( $args['id'] ),
					checked( $value, 'on', false )
				);
			}
		
			$html .= sprintf( '%1$s</label>', $args['desc'] );
			$html .= '</fieldset>';
		
			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'fieldset' => array(),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type' => array('hidden', 'checkbox'), 
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
					'checked' => array(),
				),
				'p' => array(
					'class' => array(),
				),
			);
		
			// Sanitize and output the HTML
			echo wp_kses( $html, $allowed_html );
		}
		


		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_multicheck( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '<fieldset>';
			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[ $key ] ) ? $value[ $key ] : '0';
				$html   .= sprintf( '<label for="wxspc-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html   .= sprintf( '<input type="checkbox" class="checkbox" id="wxspc-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
				$html   .= sprintf( '%1$s</label><br>', $label );
			}
			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'fieldset' => array(),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type' => array(),
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
				),
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_radio( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '<fieldset>';
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<label for="wxspc-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
				$html .= sprintf( '<input type="radio" class="radio" id="wxspc-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
				$html .= sprintf( '%1$s</label><br>', $label );
			}
			$html .= $this->get_field_description( $args );
			$html .= '</fieldset>';

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'fieldset' => array(),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type' => array(),
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
				),
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_select( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			}
			$html .= sprintf( '</select>' );
			$html .= $this->get_field_description( $args );

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'select' => array(
					'class' => array(),
					'id' => array(),
					'name' => array(),
				),
				'option' => array(
					'value' => array(),
					'selected' => array(),
				),
				'p' => array(
					'class' => array(),
				),
			);
			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_textarea( $args ) {

			$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
			$html .= $this->get_field_description( $args );

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'textarea' => array(
					'rows' => array(),
					'cols' => array(),
					'class' => array(),
					'id' => array(),
					'name' => array(),
				),
				'p' => array(
					'class' => array()
				)
		
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array $args settings field args.
		 * @return string
		 */
		function callback_html( $args ) {
			echo wp_kses_post($this->get_field_description( $args ));
		}

		/**
		 * Displays a rich text textarea for a settings field
		 *
		 * @param array $args settings field args.
		 */
		function callback_wysiwyg( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

			echo '<div style="max-width: ' . esc_attr($size) . ';">';

			$editor_settings = array(
				'teeny'         => true,
				'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
				'textarea_rows' => 10,
			);
			if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
				$editor_settings = array_merge( $editor_settings, $args['options'] );
			}

			wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

			echo '</div>';

			echo wp_kses_post($this->get_field_description( $args ));
		}

		/**
		 * Displays a file upload field for a settings field
		 *
		 * @param array $args settings field args.
		 */
		function callback_file( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section'] . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ?
			$args['options']['button_label'] :
			__( 'Choose File', 'single-product-customizer' );

			$html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
			$html .= $this->get_field_description( $args );

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'input' => array(
					'type' => array('text', 'button'), 
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
				),
				'p' => array(
					'class' => array()
				)
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays an image upload field with a preview
		 *
		 * @param array $args settings field args.
		 */
		function callback_image( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
			$id    = $args['section'] . '[' . $args['id'] . ']';
			$label = isset( $args['options']['button_label'] ) ?
			$args['options']['button_label'] :
			__( 'Choose Image', 'single-product-customizer' );

			$html  = sprintf( '<input type="text" class="%1$s-text wpsa-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= '<input type="button" class="button wpsa-browse" value="' . $label . '" />';
			$html .= $this->get_field_description( $args );
			$html .= '<p class="wpsa-image-preview"><img src=""/></p>';

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'input' => array(
					'type' => array('text', 'button'), 
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
				),
				'p' => array(
					'class' => array(),
					'img' => array(
						'src' => array()
					)
				)
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a password field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_password( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= $this->get_field_description( $args );

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'input' => array(
					'type' => array('text', 'button'), 
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
				),
				'p' => array(
					'class' => array()
				)
			);

			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}

		/**
		 * Displays a color picker field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_color( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'], $args['placeholder'] ) );
			$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html  = sprintf( '<input type="text" class="%1$s-text color-picker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" placeholder="%6$s" />', $size, $args['section'], $args['id'], $value, $args['std'], $args['placeholder'] );
			$html .= $this->get_field_description( $args );

			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'input' => array(
					'type' => array('text'), 
					'class' => array(),
					'id' => array(),
					'name' => array(),
					'value' => array(),
					'data-default-color' => array(),
					'placeholder' => array(),
				),
				'p' => array(
					'class' => array()
				)
			);
			
			// Sanitize and output the HTML
			echo wp_kses($html, $allowed_html);
		}


		/**
		 * Displays a separator field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_separator( $args ) {
			$type = isset( $args['type'] ) ? $args['type'] : 'separator';

			$html  = '';
			$html .= '<div class="wpsa-settings-separator"></div>';
			echo esc_html($html);
		}


		/**
		 * Get the value of a settings field
		 *
		 * @param string $option  settings field name.
		 * @param string $section the section name this field belongs to.
		 * @param string $default default text if it's not found.
		 * @return string
		 */
		function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return $default;
		}

		/**
		 * Add submenu page to the Settings main menu.
		 *
		 * @param string $page_title
		 * @param string $menu_title
		 * @param string $capability
		 * @param string $menu_slug
		 * @param callable $function = ''
		 * @author Ahmad Awais
		 * @since  [version]
		 */

        // register admin menu
        public function sppcfw_register_admin_menu(){
            add_submenu_page(
                'edit.php?post_type=product',
                __( 'Single Product Customizer', 'single-product-customizer' ),
                __( 'Single Product Customizer', 'single-product-customizer' ),
                'manage_options',
                'sppcfw-single-product-customizer',
                array($this,'plugin_page'),
                5
            );
        }

		public function plugin_page() {

            ?>

            <div class="tab-container-sppcfw">
                <div class="tab-sppcfw">
                    <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'images/logo.png'); ?>" alt="Site Logo" width="100%">
                    <button style="padding: 8px" class="tablinks-sppcfw" onclick="opensppcfw(event, 'basic')" id="defaultOpen"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e(' Basic Settings', 'single-product-customizer'); ?></button>
                    <button style="padding: 8px" class="tablinks-sppcfw" onclick="opensppcfw(event, 'advance')"><span class="dashicons dashicons-admin-settings"></span><?php esc_html_e(' Advance Settings', 'single-product-customizer'); ?></button>
                    <button style="padding: 8px" class="tablinks-sppcfw" onclick="opensppcfw(event, 'support')"><span class="dashicons dashicons-admin-site"></span><?php esc_html_e(' Support', 'single-product-customizer'); ?></button>
                </div>

                <div id="basic" class="tabcontent-sppcfw active">
                    <div class="metabox-holder">
                        <div id="sppcfw_basic" class="group">
                            <form method="post" action="options.php">
                                <?php
                                settings_fields('sppcfw_basic');
                                do_settings_sections('sppcfw_basic');
                                submit_button(null, 'primary', 'submit_sppcfw_basic');
                                ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="advance" class="tabcontent-sppcfw">
                    <div class="metabox-holder">
                        <div id="sppcfw_advanced" class="group">
                            <form method="post" action="options.php">
                                <?php
                                settings_fields('sppcfw_advanced');
                                do_settings_sections('sppcfw_advanced');
                                submit_button(null, 'primary', 'submit_sppcfw_advanced');
                                ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="support" class="tabcontent-sppcfw">
                    <div class="grid-support">
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-admin-site-alt3"></span>
                                <?php echo esc_html('Website:','variation-monster'); ?></strong>
                            <a href="https://www.wooxperto.com/" target="_blank"><?php echo esc_html('wooxperto.com','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Visit our official website for live chat and more information, tutorials, and resources.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-facebook-alt"></span><?php echo esc_html('Facebook:','single-product-customizer'); ?></strong>
                            <a href="https://www.facebook.com/wooxpertollc" target="_blank"><?php echo esc_html('Follow us','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Join our community on Facebook for support, updates, and discussions.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-whatsapp"></span> <?php echo esc_html('WhatsApp:','single-product-customizer'); ?></strong>
                            <a href="https://wa.me/01926167151" target="_blank"><?php echo esc_html('Chat Now ','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Get instant support by chatting with us on WhatsApp. We’re here to help!','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-email-alt"></span> <?php echo esc_html('Email:','single-product-customizer'); ?></strong> <a href="mailto:support@wooxperto.com"><?php echo esc_html('support@wooxperto.com','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Feel free to reach out to us via email for any inquiries or support requests.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-linkedin"></span> <?php echo esc_html('LinkedIn:','single-product-customizer'); ?></strong>
                            <a href="https://www.linkedin.com/company/wooxpertollc/" target="_blank"><?php echo esc_html('Connect on LinkedIn','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Let’s connect on LinkedIn for networking, updates, and professional support.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-twitter"></span> <?php echo esc_html('Twitter:','single-product-customizer'); ?></strong> <a href="https://x.com/wooxpertollc" target="_blank"><?php echo esc_html('Follow us','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Stay updated with the latest news and announcements by following us on Twitter.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-youtube"></span> <?php echo esc_html('YouTube:','single-product-customizer'); ?></strong> <a href="https://www.youtube.com/@wooxpertollc" target="_blank"><?php echo esc_html('Subscribe','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('Check out our YouTube channel for video tutorials and product showcases.','single-product-customizer'); ?></p>
                        </div>
                        <div class="support-item">
                            <strong><span class="dashicons dashicons-instagram"></span> <?php echo esc_html('Instagram:','single-product-customizer'); ?></strong>
                            <a href="https://www.instagram.com/wooxpertollc" target="_blank"><?php echo esc_html('Follow us','single-product-customizer'); ?></a>
                            <p><?php echo esc_html('See behind-the-scenes content and our latest updates on Instagram.','single-product-customizer'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
		}

		/**
		 * Show navigations as tab
		 *
		 * Shows all the settings section labels as tab
		 */
		function show_navigation() {
			$html = '<h2 class="nav-tab-wrapper">';
		
			foreach ( $this->sections_array as $tab ) {
				if ( $tab['id'] === 'sppcfw_advanced_get_pro' ) {
					$html .= '<button type="button" class="nav-tab custom-button" onclick="generate_custom_link(this)" data-url="https://www.wooxperto.com/single-product-page-customizer/">' 
						   . esc_html( $tab['title'] ) 
						   . '<span style="color: red;"> *</span></button>';
				} elseif ( $tab['id'] === 'sppcfw_advanced_get_support' ) {
					$html .= '<button type="button" class="nav-tab custom-button" onclick="generate_custom_link(this)" data-url="https://www.wooxperto.com/">' 
						   . esc_html( $tab['title'] ) 
						   . '</button>';
				} else {
					$html .= sprintf(
						'<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>',
						esc_attr( $tab['id'] ),
						esc_html( $tab['title'] )
					);
				}
			}
		
			$html .= '</h2>';
		
			// Define allowed HTML tags and attributes
			$allowed_html = array(
				'h2' => array(
					'class' => array(),
				),
				'a' => array(
					'href' => array(),
					'class' => array(),
					'id' => array(),
				),
				'button' => array(
					'type' => array(),
					'class' => array(),
					'onclick' => array(),
					'data-url' => array(),
				),
				'span' => array( 'style' => array() ),
			);
		
			// Sanitize and output the HTML
			echo wp_kses( $html, $allowed_html );

		}

		

		/**
		 * Show the section settings forms
		 *
		 * This function displays every sections in a different form
		 */
		function show_forms() {
			?>
			<div class="metabox-holder">
				<?php foreach ( $this->sections_array as $form ) { ?>
					<!-- style="display: none;" -->
					<div id="<?php echo esc_attr($form['id']); ?>" class="group" >
						<form method="post" action="options.php">
							<?php
							do_action( 'sppcfw_wsa_form_top_' . $form['id'], $form );
							settings_fields( $form['id'] );
							do_settings_sections( $form['id'] );
							do_action( 'sppcfw_wsa_form_bottom_' . $form['id'], $form );
							?>
							<div style="padding-left: 10px">
								<?php submit_button(null, 'primary', 'submit_'.$form['id']); ?>
							</div>
						</form>
					</div>
				<?php } ?>
			</div>
			<?php
			
		}

	} 

endif;
