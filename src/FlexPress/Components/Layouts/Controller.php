<?php

namespace FlexPress\Components\Layouts;

class Controller
{

    // =============
    // ! PROPERTIES
    // =============

    /**
     * @var array
     */
    protected $layouts;

    // ==============
    // ! CONSTRUCTOR
    // ==============

    /**
     * @param array $layouts
     * @throws \RuntimeException
     */
    public function __construct(array $layouts)
    {

        $this->layouts = array();

        if (!empty($layouts)) {

            foreach ($layouts as $layout) {

                if (!$layout instanceof AbstractLayout) {

                    $message = "One or more of the layouts you have passed to ";
                    $message .= get_class($this);
                    $message .= " does not implement the AbstractLayout interface.";
                    throw new \RuntimeException($message);

                }

                $this->layouts[$layout->getName()] = $layout;

            }

        }

    }

    // ==================
    // ! METHODS
    // ==================

    /**
     *
     * Used to output layouts for the site(setup in options)
     *
     * @param string $fieldName
     * @author Tim Perry
     *
     */
    public function theSiteLayouts($fieldName)
    {
        if ($layoutObjects = $this->getLayoutObjects($fieldName, "options")) {
            $this->outputLayouts($layoutObjects);
        }
    }

    /**
     *
     * Used to output the layouts got the current page
     *
     * @param string $fieldName
     * @param bool $postID
     * @author Tim Perry
     */
    public function thePageLayouts($fieldName, $postID = false)
    {
        if ($layoutObjects = $this->getLayoutObjects($fieldName, $postID)) {
            $this->outputLayouts($layoutObjects);
        }
    }

    /**
     *
     * Given a array of layouts it will call getMarkup on them,
     * and output the markup
     *
     * @param $layoutObjects
     * @return bool
     * @author Tim Perry
     *
     */
    protected function outputLayouts($layoutObjects)
    {
        if (!is_array($layoutObjects)
            || empty($layoutObjects)
        ) {
            return;
        }

        foreach ($layoutObjects as $layout_object) {

            $layout_object->getMarkup();

        }
    }

    /**
     *
     * Calls get_field for the given fieldname and postID. Then maps the data returned to the actual
     * layouts class
     *
     * @param string $acf_field_name
     * @param bool $post_id
     * @return array
     * @author Tim Perry
     *
     */
    public function getLayoutObjects($acf_field_name, $post_id = false)
    {

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        // No fieldGroups, return false
        if (!$layoutFields = get_field($acf_field_name, $post_id)) {
            return false;
        }

        // Not an array of array is empty, return false
        if (!is_array($layoutFields)
            || empty($layoutFields)
        ) {
            return false;
        }

        $layoutsObjects = array();

        foreach ($layoutFields as $key => $layoutField) {

            // sometimes acf returns us the fieldname as a string
            // but some other times it returns it in an array (when you have subfields)
            // so this gets the correct fieldname/offset
            $offset = $layoutField;
            if (is_array($layoutField)) {
                $offset = $layoutField['acf_fc_layout'];
            }

            // We store the field with its name as the key,
            // so we can use offsetGet to grab it and clone it so it
            // can used multiple times.
            if (!$layoutObject = clone $this->layouts[$offset]) {
                continue;
            }

            if (!$layoutObject->isAvailableOn($post_id)) {
                continue;
            }

            if (!is_array($layoutField)) {
                $layoutField = array($layoutField);
            }

            $layoutObject->setField($layoutField);
            $layoutObject->setID($layoutObject->getName() . "-" . $key);

            $layoutsObjects[] = $layoutObject;

        }

        return $layoutsObjects;

    }

    /**
     *
     * Used by the acf layout field when getting what subfields/layouts it has
     * (we inject the fieldGroups instead of acf loading them from the database)
     *
     * @return array
     * @author Tim Perry
     *
     */
    public function getFieldLayouts()
    {

        $layoutFields = array();

        foreach ($this->layouts as $layout) {

            // Not available, continue
            if (!$layout->isAvailableOn(get_the_ID())) {
                continue;
            }

            $layoutFields[] = array(

                "label" => $layout->getLabel(),
                "name" => $layout->getName(),
                "display" => "row",
                "sub_fields" => $layout->getFields()

            );

        }

        return $layoutFields;

    }

}
