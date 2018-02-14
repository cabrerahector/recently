<?php
/**
 * Obtains translation data from objects.
 *
 * @link       http://cabrerahector.com
 * @since      2.0.0
 *
 * @package    Recently
 * @subpackage Recently/includes
 */
/**
 * Obtains translation data from objects.
 *
 * @since      2.0.0
 * @package    Recently
 * @subpackage Recently/includes
 * @author     Hector Cabrera <me@cabrerahector.com>
 */

class Recently_Translate {

    /**
     * Class instance.
     *
     * @since    2.0.0
     * @access   private
     * @var      object|Recently_Translate
     */
    private static $instance;

    /**
     * Default language code.
     *
     * @since    2.0.0
     * @access   private
     * @var      string
     */
    private $default_language;

    /**
     * Current language code.
     *
     * @since    2.0.0
     * @access   private
     * @var      string
     */
    private $current_language;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    2.0.0
     * @access   private
     */
    private function __construct() {

        $this->default_language = apply_filters( 'wpml_default_language', NULL );
        $this->current_language = apply_filters( 'wpml_current_language', NULL );

    }

    /**
     * Get an instance of this class.
     *
     * @since    2.0.0
     * @return object|\Recently_Translate
     */
    public static function get_instance() {

        if ( is_null(self::$instance) ) {
            self::$instance = new Recently_Translate();
        }

        return self::$instance;

    }

    /*
     * Retrieves the code of the default language.
     *
     * @since    2.0.0
     * @return   string|null
     */
    public function get_default_language() {
        return $this->default_language;
    }

    /*
     * Retrieves the code of the currently active language.
     *
     * @since    2.0.0
     * @return   string|null
     */
    public function get_current_language() {
        return $this->current_language;
    }

    /*
     * Retrieves the ID of an object.
     *
     * @since    2.0.0
     * @param    integer    $object_id
     * @param    string     $object_type
     * @param    boolean    $return_original_if_missing
     * @param    string     $lang_code
     * @return   integer
     */
    public function get_object_id( $object_id = null, $object_type = 'post', $return_original_if_missing = true, $lang_code = null ) {

        return apply_filters(
            'wpml_object_id',
            $object_id,
            $object_type,
            $return_original_if_missing,
            $lang_code
        );

    }

    /*
     * Retrieves the language code of an object.
     *
     * @since    2.0.0
     * @param    integer    $object_id
     * @param    string     $object_type
     * @return   string|null
     */
    public function get_object_lang_code( $object_id = null, $object_type = 'post' ) {

        return apply_filters(
            'wpml_element_language_code',
            null,
            array(
                'element_id' => $object_id,
                'element_type' => $object_type
            )
        );

    }

} // End Recently_Translate class
