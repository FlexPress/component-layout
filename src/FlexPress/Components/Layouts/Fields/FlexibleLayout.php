<?php

namespace FlexPress\Components\Layouts\Fields;

use FlexPress\Components\ACF\FieldTrait;
use FlexPress\Components\Layouts\Controller;

if(!class_exists('acf_field_flexible_content')) {
    throw new \RuntimeException('Please add and enable the flexilbe content add on for acf before trying to use the flexible layouts field.');
}

class FlexibleLayout extends \acf_field_flexible_content
{

    /**
     * @var \FlexPress\Components\Layouts\Controller
     */
    protected $controller;

    /**
     * @param Controller $controller
     */
    function __construct(Controller $controller)
    {

        $this->controller = $controller;

        // vars
        $this->name = 'flexible_layout';
        $this->label = __("Flexible Layout", 'acf');
        $this->category = __("Layout", 'acf');

        \acf_field::__construct();

    }

    /**
     *
     * Loads the field
     *
     * @param $field
     * @return mixed
     * @author Tim Perry
     *
     */
    function load_field($field)
    {

        // inject the layouts here
        $field['layouts'] = $this->controller->getFieldLayouts();
        return parent::load_field($field);

    }

    /**
     *
     * Creates the field
     *
     * @param $field
     * @return mixed
     * @author Tim Perry
     *
     */
    function create_field($field)
    {

        // this will need to be change to render_field for ACF5

        // inject the layouts here
        $field['button_label'] = "Add Layout";
        $field['layouts'] = $this->controller->getFieldLayouts();

        return parent::create_field($field);

    }

    function create_options($field)
    {
        ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Layout", 'acf'); ?></label>
            </td>
            <td>
                <p class="description"><?php _e(
                        "To add layouts, create a concreate class that subclasses the Layout class and add it in when using the LayoutsController.",
                        "acf"
                    ); ?></p>
            </td>
        </tr>
    <?php
    }
}
