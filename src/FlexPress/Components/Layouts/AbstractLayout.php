<?php

namespace FlexPress\Components\Layouts;

abstract class AbstractLayout
{

    // ===================
    // ! PROPERTIES
    // ===================

    /**
     * The acf field array
     * @var array
     */
    protected $field;

    /**
     * The id for the layout
     * @var string
     */
    protected $id;

    // ===================
    // ! SETTERS
    // ===================

    /**
     * @param mixed $field
     */
    public function setField(array $field)
    {
        $this->field = $field;
    }

    /**
     * @param mixed $id
     */
    public function setID($id)
    {
        $this->id = $id;
    }

    // ===================
    // ! GETTERS
    // ===================

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     *
     * Gets the layouts name, defaults to a old style namespaced name
     * using underscores in place of backslashes
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    public function getName()
    {
        return str_replace("\\", "_", get_class($this));
    }

    // ===================
    // ! METHODS
    // ===================

    /**
     *
     * Returns the field array
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    public function getFields()
    {

        return array(

            array(

                'key' => uniqid("field_"),
                'label' => 'No configuration needed',
                'name' => '',
                'type' => 'message',
                'message' => 'This layout has no configurable options.'

            )

        );

    }

    /**
     *
     * Given the post id and post type, return if this layout is available
     *
     * @param $post_id
     * @return mixed
     * @author Tim Perry
     *
     */
    public function isAvailableOn($post_id)
    {
        return true;
    }

    // ===================
    // ! REQUIRED METHODS
    // ===================

    /**
     *
     * Nicename label for the layout
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    abstract public function getLabel();

    /**
     *
     * Returns the markup for the layout
     *
     * @return mixed
     * @author Tim Perry
     *
     */
    abstract public function getMarkup();

}