
# FlexPress layout component

## Please note
This component requires the ACF Flexible Content Field, which can be purchased here http://www.advancedcustomfields.com/add-ons/flexible-content-field/. Install and activate before continuing.

## Install with Pimple

The layouts system is more complex than most of the other components as you have three main parts to it, of which are fairly compicated on their own:
- LayoutsController - Acts as a container for the layouts but also has helper functions to load data and output them.
- FlexibleLayout - ACF field.
- FlexibleLayoutProxy - As described in the comments below, require becuase of the way ACF fields work.

```
// Layout controller, you add your layouts here and use the controller to output them
$pimple["layoutController"] = function ($c) {
    return new LayoutController(array(
      // Add your layouts here
    ));
};

// an acf field that allows you to use the layoutController to add layouts instead of having to repeat them for each field group
$pimple["flexibleLayout"] = function ($c) {
    return new FlexibleLayout($c["layoutController"]);
};

// a proxy class that is required as when you create a acf field it runs all the hooks and we dont want to do that when we create an instance of it to pass to the fieldshelper, so we use a fieldproxy.
$this["flexibleLayoutProxy"] = function ($c) {
    return new FlexibleLayoutProxy($c);
};

```
You will also need to use the ACF component and register the field like this:
```
$pimple['ACFHelper'] = function ($c) {
    return new ACFHelper($c['objectStorage'], $c['objectStorage'], array(), array(
        $c["flexibleLayoutProxy"]
    ));
};
```
- Note the objectStorage config is a SPLObjectStorage class

## Creating a concreate layout class
- For each layout that exists you need to create a class that extends AbstractLayout, which means implementing the getLabel() and getMarkup() methods as a minimum.
- Here is a very simple example that output hello world:
```
class HelloWorld extends AbstractLayout {
  
  public function getLabel() {
    return "Hello world";
  }
  
  public function getMarkup() {
    echo "<p>Hello world</p>";
  }
  
}
```
Now that we have create a layout we need to add it to the LayoutsController:
```
$pimple["helloWorldLayout"] = function() {
  return new HelloWorld();
};

$pimple["layoutController"] = function ($c) {
    return new LayoutController(array(
      // Add your layouts here
      $c["helloWorldLayout"]
    ));
};
```

- You will want to output the layout somewhere so create a acf field group in the admin section, and select the field type as FlexibleLayout.
- Next go to wherever you setup the fieldgroup and click the add layout button, the popup should just show 'Hello world', click on that and it will inform you that there are no configurable options on it and save/publish the page.
- Finally you need to add the code to output the layouts:
```
$layoutsController = $pimple['layoutsController'];
$layoutsController->thePageLayouts('<your_field_name_here>');
```
Make sure you change the <your_field_name_here> to whatever you called your field when setting up the acf and you should be all done.

## Advanced layouts
Previous examples showed very simple layouts with no options, this next example will show you how to add acf field to a layout and specifiy if the layout is available for the given post_id:

```
class HelloUniverse extends AbstractLayout {
  
    public function getLabel() {
        return "Hello universe";
    }
    
    public function getMarkup() {
        echo "<p>Hello universe</p>";
        if($this->field['fp_show_time']) { 
            echo "<p>", date(), "</p>";
        }
        
    }
    
    // make the layout only available on pages
    public function isAvailableOn($post_id) {
        return get_post_type($post_id) == 'page';
    }
  
    // to get this array, export a acf field group and grab everything in 'fields' => array(...etc
    public function getFields() {
        return array (
            array (
                'key' => 'field_53b132d9363ec',
                'label' => 'Show time',
                'name' => 'fp_show_time',
                'type' => 'true_false',
                'message' => '',
                'default_value' => 0
            )
        );
    }
  
}
```
So what has changed from the hello world layout? Well we have added the use of a field, we did this my implementing the getFields() method and returning the acf field. As the comment in the code mentions you can get that by exporting a acf field group and extracting everything for the key 'fields' => etc. We have then utilised that in the getMarkup by using the field propery, this is the acf field that you would get back if you did get_field(<field_name>).

Finally we have also implemented the isAvailableOn() method, this gives us the post id and we can return a boolean value for if it is available. In this example we have make it so the layout is only available on pages.

## LayoutController
We have showed you how the AbstractLayout works but there are a few other methods on the LayoutController that might be useful. 

### Public methods
- theSiteLayouts($fieldName) - Outputs the layouts for the site, when stored in options instead of postmeta
- thePageLayouts($fieldName, $postId = false) - Outputs the layouts for the current page, you can specify the postId as the second param
- getLayoutObjects() - used by the by the two methods above to grab the layout data from the database.
- getFieldLayouts() - used by the FlexibleLayout field to get all the field objects.

### Protected methods
- outputLayouts($layouts) - Outputs the layouts, used by the other output methods like thePageLayouts, simply loops the given layouts and calls getMarkup() on them.
