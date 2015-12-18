<?php

/*
Plugin Name: Lion2486 example Widget
Plugin URI: http://codescar.eu
Description: An example widget using Lion2486 Widget Class.
Version: 1.0
Author: lion2486
Author URI: http://codescar.eu
License: None
*/

/**
 * Example Widget based on lion2486-widget class.
 */

require_once( __DIR__ . '/lion2486-widget.php' );

class Lion2486_Example extends Lion2486_Widget{

	//The widget ID, must be unique! (optional, if NULL, class name is used).
	protected $WidgetID = NULL;
	//WidgetDescription. Text to show on widget list (optional).
	protected $WidgetDescription = "Lion2486 Sample Widget.";

	//text domain for translations.
	protected $textDomain = 'html5blank';
	//Widget Name! (Human readable form)
	protected $WidgetName = "Example Widget";

	public function __construct(  ) {
		//Call the parent::__constructor()
		parent::__construct();

		//Here set your own fields.
		$this->fields = array(
			$this->field(
				'title',                    //Name of the field
				'Title',                    //Title (auto translation supported)
				'The widget title field.',  //Description (auto translation supported)
				'text',                     //Type of the field
				'Enter Title',              //Default value
				'',                         //Current value
				$this->textDomain           //text-domain to use
			),
			$this->field(
				'html',
				__('Html Body', $this->textDomain),
				'',
				'html',
				'html here...'
			),
			$this->field(
				'image',
				'Image',
				'',
				'media'
			)
		);
	}

	//For custom layout overwrite the widget() method as you wish!

//	public function widget( $args, $instance ) {
//		extract( $vars = $this->form_fields( $instance ) );
//
//		echo $vars['title'];
//
//	}
};

new Lion2486_Example();