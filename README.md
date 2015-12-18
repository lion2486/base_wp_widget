# base_wp_widget
A basic Wordpress Widget class to extend and easy create your own custom widgets.

# Usage
In order to create fast a wordpress widget, without wondering about admin side and saving-update methods just follow these steps:

1. Download lion2486-widget.php file.
2. Include it to your file (with `require_once( __DIR__ . '/lion2486-widget.php' );`).
3. Extend the class Lion2486_Widget (like `class Lion2486_Example extends Lion2486_Widget`).
4. Overwrite class fields $WidgetID, $WidgetName, $WidgetDescription and $textDomain. 
5. Create a class constructor (`__construct()`) that calls `parent::__construct()` and define your widget fields into `$this->fields` class field as following
 ```
 $this->fields = array(
    $this->field(
        'title',                    //Name of the field
        'Title',                    //Title (auto translation supported)
        'The widget title field.',  //Description (auto translation supported)
        'text',                     //Type of the field
        'Enter Title',              //Default value
        '',                         //Current value
        $this->textDomain           //text-domain to use
    )
   // , more fields here
);
 ```
6. Overwrite the method `public function widget( $args, $instance )`  to display your widget with your way!
7. Create an object of your class (like `new Lion2486_Example();`).
8. READY!

For some help see example-widget file.

### NOTE:
If you want to use your widget like an standalone plugin, you have to include the wordpress plugin comment into the file like below
```
/*
Plugin Name: Lion2486 example Widget
Plugin URI: http://codescar.eu
Description: An example widget using Lion2486 Widget Class.
Version: 1.0
Author: lion2486
Author URI: http://codescar.eu
License: None
*/
```

# Documentation
You can see the class and method documentation into source file for more info, some basic and useful information below

## Field Types

  Acceptable values of types:
         String               -       Array
         text                 -       array('dataType' => 'text', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
         textarea             -       array('dataType' => 'textarea', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
         media                -       array('dataType' => 'media', 'attr' => array( 'data-my' => 'my', 'length' => '10' ) )
                              -       array('dataType' => 'input', 'inputTag' => 'HTML_TAG', ...)
         wysiwyg              -       array('dataType' => 'wysiwyg', ...)
         html                 -       array('dataType' => 'html', ...)

## Examples of the widget() method implementations

- Default method
   ```
   public function widget( $args, $instance ) {
   
       //Provides all registered instance's variables and values local accessible like $title and into $vars array.
       extract( $vars = $this->form_fields( $instance ) );
   
       foreach( $this->fields as $var ){
           if( $var instanceof Lion2486_Widget_fieldset )
               echo $var->displayValue( $instance );
       }
   
   }
   ```
   
- Custom method
```
public function widget( $args, $instance ) {

    //Provides all registered instance's variables and values local accessible like $title and into $vars array.
    extract( $vars = $this->form_fields( $instance ) );

    //we have at least 2 fields with names title and body.
    echo "<h1>{$vars['title']}</h1>";       //OR echo "<h1>{$title}</h1>";
    echo "<p>{$vars['body']}</p>";          //OR echo "<p>{$body}</p>";

}
```   

# Notes
It is an initial version, more field types will be included and a better handling of the media are in schedule. Thank you.