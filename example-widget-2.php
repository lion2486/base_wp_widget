<?php

/*
Plugin Name: Lion2486 example Widget 2
Plugin URI: http://codescar.eu
Description: An example widget using Lion2486 Widget Class.
Version: 1.01
Author: lion2486
Author URI: http://codescar.eu
License: None
*/

/**
 * Example Widget 2 based on lion2486-widget class.
 */

require_once( __DIR__ . '/lion2486-widget.php' );

class Image_Page_header_Widget extends Lion2486_Widget {
	//The widget ID, must be unique! (optional, if NULL, class name is used).
	protected $WidgetID = "image_page_header_widget";
	//WidgetDescription. Text to show on widget list (optional).
	protected $WidgetDescription = "Widget to display an image as header of the page.";

	//text domain for translations.
	protected $textDomain = 'html5blank';
	//Widget Name! (Human readable form)
	protected $WidgetName = "Page Image Header";

	public function __construct(  ) {
		//Call the parent::__constructor()
		parent::__construct();

		//Here set your own fields.
		$this->fields = array(
			$this->field(
				'image_id',
				'Image',
				'',
				'media'
			),
			$this->field(
				'icon_id',
				'Icon',
				'',
				'media'
			),
			$this->field(
				'small_title',
				'Small Title'
			),
			$this->field(
				'title',
				'Title'
			),
			$this->field(
				'body',
				'Text Body',
				'',
				'textarea'
			),
			$this->field(
				'bg_color',
				'Background Color',
				'',
				array(
					'dataType'  => 'input',
					'attr'      => array(
						'type'      => 'color'
					)
				),
				'#143260'
			),
			$this->field(
				'opacity',
				'Background Opacity',
				'',
				array(
					'dataType'  => 'input',
					'attr'      => array(
						'type'  => 'range',
						'min'   => '0',
						'max'   => '1',
						'step'  => '0.05'
					)
				),
				'0.35'
			)


		);
	}

	public function widget( $args, $instance ) {
		extract( $vars = $this->form_fields( $instance ) );

		$page = get_post( $vars['page_id'] );

		if(!$page)
			return;
		echo "
			<div class='page-header-image-container' style='
				background-image: url(\"" . wp_get_attachment_url( $vars['image_id'] ) . "\");
				background-size: cover;
				//position: absolute;
				top: 0;
				width: 100%;
				height: 46vw;
			' >
				<div class='content' style='
					width: 100%;
					height: 100%;
					background-color: {$vars['bg_color']};
					opacity: {$vars['opacity']};
				'>
					<img src='" . wp_get_attachment_url( $vars['icon_id']) . "' alt='{$vars['title']}' />
					<h4>{$vars['small_title']}</h4>
				    <h1>{$vars['title']}</h1>
					<p>{$vars['body']}</p>
				</div>
           	</div>";
	}
};

new Image_Page_header_Widget();