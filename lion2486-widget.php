<?php

$base_wp_widget_version = 1.01;

if( !defined( 'base_wp_widget_version' ) ){
	define( 'base_wp_widget_version', $base_wp_widget_version );
	define( 'base_wp_widget_file', __FILE__  );
}else if( $base_wp_widget_version >= base_wp_widget_version ){
	//You have an older version of the file loaded!
	wp_die('There is an outdated widget library already loaded. The outdated file is: ' . base_wp_widget_file .'\n
			Please remove it to load the latest version of the base_wp_widget class.');
}



if( ! class_exists( 'Lion2486_Widget_fieldset' ) ){
	/**
	 * Class Lion2486_Widget_fieldset
	 */
	class Lion2486_Widget_fieldset{
		public $name;       //The name-slug of the field. Unique per widget.
		public $title;      //The title of the field. It used in the option label.
		public $description;//The description of the field or instructions.
		public $type;       //The type of the field. Can be text/textarea/input...
		public $attr;       //Custom attributes for the admin input field
		public $inputTag;   //The HTML Tag to use with $type="input".
		public $options;    //All other options passed into type array
		public $default;    //The default value of the field.
		public $value;      //The current value of the field.
		public $textDomain; //The text-domain to use.

		/**
		* Lion2486_Widget_fieldset constructor.
	    * Creates a fieldSet Object to use it in a Widget.
		*
		* @param string $name The name of the field.
	    *                       Optionally you can pass as an arguement an array with the arguments names as keys.
		* @param string $title The title of the field. This value has automatic translation support.
	    *                       (Optional) If empty, it will be auto filled with CamelCase of the name.
		* @param string $description The description of the field. This value has automatic translation support.
		* @param mixed $type The type of the field. It can be a string or an array with 'dataType' key.
	    *                       Acceptable values of types:
	    *                              String               -       Array
	    *                              text                 -       array('dataType' => 'text', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
		*                              textarea             -       array('dataType' => 'textarea', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
		*                              media                -       array('dataType' => 'media', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
		*                                                   -       array('dataType' => 'input', 'inputTag' => 'HTML_TAG', ...)
		*                              wysiwyg              -       array('dataType' => 'wysiwyg', ...)
		*                              html                 -       array('dataType' => 'html', ...)
		*
		*
		*
		* @param null $default The default value of the field.
		* @param mixed $value The current value of the field. (Usually pass $instance[name])
	    * @param string $textDomain The text-domain to use.
		*/
		public function __construct( $name = '', $title = '', $description = '', $type = 'text',
			$default = null, $value = null, $textDomain = '' ) {

			if( is_array( $name ) ){
				extract( $name );
			}

			if( ! empty( $textDomain ) ){
				$this->textDomain = $textDomain;
			}else{
				$this->textDomain = 'lion2486_textdomain';
			}

			if( ! empty( $name ) ){
				$this->name = $name;
			}

			if( ! empty( $title ) ){
				$this->title = $title;
			}
			else
				if( ! empty( $name ) ){
					$this->title = __( ucwords( str_replace( array('-', '_', '.'), " ", $title ) ), $this->textDomain );
				}

			if( ! empty( $description ) ){
				$this->description = __( $description, $this->textDomain );
			}

			if( is_array( $type ) ){
				$this->type = $type['dataType'];
				$this->attr = array_key_exists( 'attr', $type ) ? $type['attr'] : '';
				$this->inputTag = array_key_exists( 'intputTag', $type ) ? $type['inputTag'] : 'input';

				unset( $type['dataType'] );
				unset( $type['inputTag'] );
				unset( $type['attr'] );

				$this->options = $type;
			}else{
				$this->type = $type;
				$this->attr = '';
				$this->inputTag = 'input';
			}

			if( "media" == $this->type ){
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueueMedia' ) );
			}

			$this->default = apply_filters( 'Lion2486_filter_value', $default );
			if( ! $value ){
				$this->value = apply_filters( 'Lion2486_filter_value', $default );
			}else{
				$this->value = apply_filters( 'Lion2486_filter_value', $value );
			}

		}

		/**
		 * Lion2486_Widget_fielset::displayValue
		 * This method is used to display the field value into the frontend if no custom view is set.
		 *
		 * @param array $instance (optional)
		 *
		 * @return string the html to display.
		 */
		public function displayValue( $instance = null ) {
			$this->value = ( $instance && array_key_exists( $this->name, $instance ) )
				? apply_filters( 'Lion2486_filter_value', $instance[ $this->name ] )
				: apply_filters( 'Lion2486_filter_value', $this->value );

			if( empty( $this->value ) )
				return "";

			$return = "";

			if( "text" == $this->type ){
				$return .= "<span>" . esc_attr( $this->value ) . "</span>";
			}elseif( "textarea" == $this->type){
				$return .= "<p>" . esc_attr( $this->value ) . "</p>";
			}elseif( "html" == $this->type){
				$return .= $this->value;
			}elseif( "input" == $this->type ) {
				$return .= "<span>{$this->value}</span>";
			}elseif( "media" == $this->type ){
				$title = get_the_title( $this->value );
				$url = wp_get_attachment_url( $this->value );

				//Check what type of media the value is
				if( wp_attachment_is_image( $this->value ) ){
					$src = wp_get_attachment_image_src( $this->value );
					$return .= "<img src=\"{$url}\" alt=\"{$title}\" title=\"{$title}\" />";
				}else{

					$return .= "<a href=\"{$url}\">{$title}</a>";
				}
			}else{
				$return .= $this->value;
			}

			return $return;
		}

	    /**
	    * Lion2486_Widget_fielset::formField
        *
		* @param $widget The widget object, usually pass $this.
		* @param $instance The widget instance in order to fetch the field value.
	    *                   If field value is set, it is not needed.
		 *
		* @return string The HTML for the widget form.
		 */
		public function formField( $widget, $instance = null ) {

			//TODO add apply_filters
			//TODO display based on  template

			$this->value = ( $instance && array_key_exists( $this->name, $instance ) )
				? apply_filters( 'Lion2486_filter_value', $instance[ $this->name ] )
				: apply_filters( 'Lion2486_filter_value', $this->value );

			$field_id = $widget->get_field_id($this->name);

			$fieldHTML = "<label for=\"" . $field_id ."\">{$this->title}</label>";

			$add_attrs = true;

			if( "text" == $this->type ){
				$openTag = "
					<input
				       type=\"text\"
				       value=\"" . esc_attr( $this->value ) . "\"
				";
				$closeTag = "/>";
			}elseif( "textarea" == $this->type){
				$openTag = "
					<textarea
				       type=\"text\"
				";
				$closeTag = ">" . esc_attr( $this->value ) . "</textarea>";
			}elseif( "html" == $this->type){
				$openTag = "
					<textarea
				       type=\"text\"
				";
				$closeTag = ">{$this->value}</textarea>";
			}elseif( "wysiwyg" == $this->type){

				$add_attrs = false;
				wp_editor( $this->value, $field_id, array( 'editor_css' => '<style>.js .tmce-active .wp-editor-area { color: black; }</style>' ) );

			}elseif( "input" == $this->type ) {
				$openTag = "<" . $this->inputTag . "
								value=\"" . $this->value . "\" ";
				$closeTag = "/>";
			}elseif( "media" == $this->type ){
				$upload_link = '';

				if( ! empty( $this->value ) )
					$image_src = wp_get_attachment_url( $this->value );

				$openTag = "
					<div id=\"{$field_id}-parent-container\">
					<div id=\"{$field_id}-img-container\">
				" .

	           ( ! empty( $this->value ) ?
		           ( wp_attachment_is_image( $this->value)
			           ? "<img src=\"{$image_src}\" alt=\"\" style=\"max-width:100%;\" />"
		               : "<a href=\"{$image_src}\">" . get_the_title( $this->value ). "</a>"
		           )
		           : ""

                )

				. "
					</div>

					<!-- Your add & remove image links -->
					<p class=\"hide-if-no-js\">
					    <a id=\"{$field_id}-upload-img\" class=\"" . ( ! empty ( $this->value ) ? 'hidden' : '') . "\"
					       href=\"$upload_link\">
					       Add a file
					    </a>
					    <a id=\"{$field_id}-delete-img\" class=\" " . ( empty( $this->value ) ? 'hidden' : '') . "\"
					      href=\"#\">
					        Remove this file
					    </a>
					</p>

					<!-- A hidden input to set and post the chosen image id -->
					<input id=\"{$field_id}-img-id\" type=\"hidden\" value=\"" . esc_attr( $this->value ) . "\"

				";
				$closeTag = "
				/>
				</div>
				<script>
					jQuery(function($){

						// Set all variables to be used in scope
						var frame,
							metaBox = $('#{$field_id}-parent-container'), // Your meta box id here
							addImgLink = metaBox.find('#{$field_id}-upload-img'),
							delImgLink = metaBox.find( '#{$field_id}-delete-img'),
							imgContainer = metaBox.find( '#{$field_id}-img-container'),
							imgIdInput = metaBox.find( '#{$field_id}-img-id' );

						// ADD IMAGE LINK
						addImgLink.on( 'click', function( event ){

							event.preventDefault();

							// If the media frame already exists, reopen it.
							if ( frame ) {
								frame.open();
								return;
							}

							// Create a new media frame
							frame = wp.media({
								title: 'Select or Upload Media Of Your Chosen Persuasion',
								button: {
									text: 'Use this media'
								},
								multiple: " . ( ( array_key_exists( 'multiple', $this->options ) && $this->options['multiple'] ) ? "true" : "false" ) ."  // Set to true to allow multiple files to be selected
							});


							// When an image is selected in the media frame...
							frame.on( 'select', function() {

								" . ( ( array_key_exists( 'multiple', $this->options ) && $this->options['multiple'] ) ?
							//If we allow multiple Files
						//TODO handle multiple files.
								"
								var attachments = frame.state().get('selection').first().toJSON();


								$.each ( attachments, function (index) {
									var attachment = attachments[index];

									if( attachment.type == \"image\" )
										imgContainer.append( '<div class=\"attachment - file attachment - image\">' +
										                        '<img src=\"'+attachment.url+'\" alt=\"\" style=\"max - width:100 %;\"/>' +
										                        '<a href=\"\" onclick=\"jQuery(this).parent().remove();\">Remove Image</a>' +
									                         '</div>' );
									else
										imgContainer.append( '<div class=\"attachment - file attachment - image\">' +
																'<a href=\"'+attachment.url+'\" >' + attachment.title + '</a>'+
																'<a href=\"\" onclick=\"jQuery(this).parent().remove();\">Remove File</a>' +
									                         '</div>' );


								});

								// Send the attachment id to our hidden input
								imgIdInput.val( attachment.id );

								// Unhide the remove image link
								delImgLink.removeClass( 'hidden' );
								" :
							//Or just a single file
								"
								var attachment = frame.state().get('selection').first().toJSON();
								// Send the attachment URL to our custom image input field.
								if( attachment.type == \"image\" )
									imgContainer.append( '<img src=\"'+attachment.url+'\" alt=\"\" style=\"max - width:100 %;\"/>' );
								else
									imgContainer.append( '<a href=\"'+attachment.url+'\" >' + attachment.title + '</a>' );
								// Send the attachment id to our hidden input
								imgIdInput.val( attachment.id );
								// Hide the add image link
								addImgLink.addClass( 'hidden' );
								// Unhide the remove image link
								delImgLink.removeClass( 'hidden' );

								" ) . "

							});

							// Finally, open the modal on click
							frame.open();
						});


						// DELETE IMAGE LINK
						delImgLink.on( 'click', function( event ){

							event.preventDefault();

							// Clear out the preview image
							imgContainer.html( '' );

							// Un-hide the add image link
							addImgLink.removeClass( 'hidden' );

							// Hide the delete image link
							delImgLink.addClass( 'hidden' );

							// Delete the image id from the hidden input
							imgIdInput.val( '' );

						});

					});
				</script>
				";
			}else{
				$openTag = "
					<input
				       type=\"text\"
				       value=\"" . esc_attr( $this->value ) . "\"
				";
				$closeTag = "/>";
			}

			$attr = '';
			if ( is_array( $this->attr ) ){
				$attr = implode( ' ',
					array_map(
						function ( $v, $k ) { return $k . '=' . "\"" . $v . "\""; },
						$this->attr,
						array_keys( $this->attr )
					)
				);
			}

			if( $add_attrs )
				$fieldHTML .= $openTag . "
					class=\"widefat\"
					       id=\"" . $field_id . "\"
					       name=\"" . $widget->get_field_name( $this->name ) . "\"
					       $attr
				" . $closeTag;

			if( ! empty( $this->description ) ){
				$fieldHTML .= "<span class=\"description\">{$this->description}</span><br/>";
			}

			return $fieldHTML;
		}

		public function enqueueMedia(){
			wp_enqueue_media();
		}
		/**
		 * Lion2486_Widget_fielset::updateField
		 *
		 * @param &$instance The instance array to be filled.
		 * @param $new_instance The new_instance from arg of update method.
		 */
		public function updateField( &$instance, $new_instance ){
			//TODO add apply_filters
			//wp_die( print_r($new_instance, true));
			$instance[ $this->name ] = ( ! empty( $new_instance[ $this->name ] ) ) ?  $new_instance[ $this->name ] : '';

			$this->value = $instance[ $this->name ];
		}

	};
}   //endif class_exists

if( ! class_exists( 'Lion2486_Widget' ) ){
	class Lion2486_Widget extends WP_Widget {

		protected $fields;
		protected $WidgetID = NULL;
		protected $WidgetName = "Lion2486 Widget";
		protected $WidgetDescription = "Lion2486 Sample Widget.";
		protected $textDomain = 'html5blank';

		/**
		* Register the widget with WordPress.
		*/
		public function __construct() {
			parent::__construct(
				$this->WidgetID, // Base ID
				__( $this->WidgetName, $this->textDomain ), // Name
				array( 'description' => __( $this->WidgetDescription, $this->textDomain ), ) // Args
			);

			if( $this->WidgetID == NULL )
				$this->WidgetID = get_class( $this );

			$this->fields = array(
				new Lion2486_Widget_fieldset(
					'title',                    //Name of the field
					'Title',                    //Title (auto translation supported)
					'The widget title field.',  //Description (auto translation supported)
					'text',                     //Type of the field
					'Enter Title',              //Default value
					'',                         //Current value
					$this->textDomain           //text-domain to use
				),
				new Lion2486_Widget_fieldset(
					'body',
					__('Html Body', $this->textDomain),
					'',
					'text',
					'text here...',
					'',
					$this->textDomain
				)
			);

			add_action( 'widgets_init', array( $this, 'register_widget' ) );
		}

		/**
		 * Lion2486_Widget::register_widget
		 *
		 * Actually register the widget into wordpress.
		 */
		public function register_widget(){
			register_widget( get_class( $this ) );
		}

		/**
		 * Lion2486_Widget::form_fields
		 *
		 * Return an array with all registered instance's variables names and values.
		 *
		 * @param $instance The widget instance.
		 *
		 * @return array An array with the actual field name and value.
		 */
		protected function form_fields( $instance ){
			$vars = array();

			foreach( $this->fields as $field ){
				$vars[$field->name] = ( array_key_exists( $field->name, $instance ) && ! empty( $instance[$field->name] ) )
					? $instance[$field->name]
					: $field->value;

				$field->value = $vars[ $field->name ];
			}

			return $vars;
		}

		protected function field( $name = '', $title = '', $description = '', $type = 'text',
			$default = null, $value = null, $textDomain = '' ){

			return new Lion2486_Widget_fieldset(
				$name,                      //Name of the field
				$title,                     //Title (auto translation supported)
				$description,               //Description (auto translation supported)
				$type,                      //Type of the field
				$default,                   //Default value
				$value,                     //Current value
				! empty( $textDomain )
					? $textDomain
					: $this->textDomain     //text-domain to use
			);

		}

		/**
		* Front-end display of widget.
		*
		* @see WP_Widget::widget()
		*
		* @param array $args     Widget arguments.
		* @param array $instance Saved values from database.
		*/
		public function widget( $args, $instance ) {

			//Provides all registered instance's variables and values local accessible like $title and into $vars array.
			extract( $vars = $this->form_fields( $instance ) );

			foreach( $this->fields as $var ){
				if( $var instanceof Lion2486_Widget_fieldset )
					echo $var->displayValue( $instance );
			}

			//print_r($vars);

		}

		/**
		* Back-end widget for

		* @see WP_Widget::form

		* @param array $instance Previously saved values from databa
		*
		* @return voids
		*/
		public function form( $instance ) {

			//Provides all registered instance's variables and values local accessible like $title and into $vars array.
			extract( $vars = $this->form_fields( $instance ) );

			echo '<p class="lion2486-widget">';

			foreach( $this->fields as $field )
				echo $field->formField( $this, $instance );

			echo '</p>';

			return;
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			foreach( $this->fields as $field )
				$field->updateField( $instance, $new_instance );

			return $instance;
		}

	};
}   //endif class_exists

