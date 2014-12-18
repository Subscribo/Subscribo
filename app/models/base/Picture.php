<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Picture
 * Automatically generated abstract model class
 *
 * @method \Model\Picture[] get() public static function get(array $columns = array()) returns an array of models Picture
 * @method null|\Model\Picture first() public static function first(array $columns = array()) returns model Picture
 *
 * @property int $id Primary key
 * @property string $comment
 * @property int $fileFormat
 * @property bool $transparentBackground
 * @property int $sizeFormat
 * @property int $originalHeight
 * @property int $originalWidth
 * @property string $originalUrl
 * @property string $smallUrl
 * @property string $mediumUrl
 * @property string $bigUrl
 * @property int $picturableId
 * @property string $picturableType
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Tag[] tags A collection of foreign models related via polymorphic many belongs to many relation 
 * @property-read \Model\AbstractModel|\Model\KochaboRecipeStep|\Model\KochaboRecipe|null picturable Foreign model related via polymorphic one belongs to one relation 
 */
abstract class Picture extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'pictures';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'comment' => array('db_type' => 'varchar'),
                                'fileFormat' => array('db_type' => 'tinyinteger'),
                                'transparentBackground' => array('db_type' => 'boolean'),
                                'sizeFormat' => array('db_type' => 'tinyinteger'),
                                'originalHeight' => array('db_type' => 'integer'),
                                'originalWidth' => array('db_type' => 'integer'),
                                'originalUrl' => array('db_type' => 'varchar'),
                                'smallUrl' => array('db_type' => 'varchar'),
                                'mediumUrl' => array('db_type' => 'varchar'),
                                'bigUrl' => array('db_type' => 'varchar'),
                                'picturableId' => array('db_type' => 'biginteger'),
                                'picturableType' => array('db_type' => 'varchar'),
                                'createdAt' => array('db_type' => 'datetime'),
                                'updatedAt' => array('db_type' => 'datetime'),
                            );


    /**
     * Property name (usually camel cased) to column (attribute) name (usually snake cased) map
     *
     * @var array
     */
    protected $attributeMap = array(
                                'id' => 'id',
                                'comment' => 'comment',
                                'fileFormat' => 'file_format',
                                'transparentBackground' => 'transparent_background',
                                'sizeFormat' => 'size_format',
                                'originalHeight' => 'original_height',
                                'originalWidth' => 'original_width',
                                'originalUrl' => 'original_url',
                                'smallUrl' => 'small_url',
                                'mediumUrl' => 'medium_url',
                                'bigUrl' => 'big_url',
                                'picturableId' => 'picturable_id',
                                'picturableType' => 'picturable_type',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'comment',
                                    'file_format',
                                    'transparent_background',
                                    'size_format',
                                    'original_height',
                                    'original_width',
                                    'original_url',
                                    'small_url',
                                    'medium_url',
                                    'big_url',
                                    'picturable_id',
                                    'picturable_type',
                                );


    /**
     * Rules for validation
     *
     * @var array
     */
    public static $rules = array(
                                    'id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                    ),

                                    'comment' => array (
                                        'max:255',
                                    ),

                                    'file_format' => array (
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'transparent_background' => array (
                                        'required',
                                        'boolean',
                                    ),

                                    'size_format' => array (
                                        'integer',
                                        'between:0,255',
                                    ),

                                    'original_height' => array (
                                        'integer',
                                        'between:0,4294967295',
                                    ),

                                    'original_width' => array (
                                        'integer',
                                        'between:0,4294967295',
                                    ),

                                    'original_url' => array (
                                        'max:255',
                                    ),

                                    'small_url' => array (
                                        'max:255',
                                    ),

                                    'medium_url' => array (
                                        'max:255',
                                    ),

                                    'big_url' => array (
                                        'max:255',
                                    ),

                                    'picturable_id' => array (
                                        'integer',
                                        'between:-9223372036854775808,9223372036854775807',
                                    ),

                                    'picturable_type' => array (
                                        'max:255',
                                    ),
                                );


    /**
     * Rules for modifications before validation
     *
     * @var array
     */
    public static $modificationRulesBeforeValidation = array();


    /**
     * Rules for modifications after validation
     *
     * @var array
     */
    public static $modificationRulesAfterValidation = array(
                                    'id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'file_format' => array (
                                        'non_printable_to_null',
                                    ),

                                    'transparent_background' => array (
                                        'non_printable_to_null',
                                    ),

                                    'size_format' => array (
                                        'non_printable_to_null',
                                    ),

                                    'original_height' => array (
                                        'non_printable_to_null',
                                    ),

                                    'original_width' => array (
                                        'non_printable_to_null',
                                    ),

                                    'picturable_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'picturable_type' => array (
                                        'non_printable_to_null',
                                    ),
                                );

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
                                        'tags' => '\\Model\\Tag',
                                        'picturable' => array(
                                            '\\Model\\KochaboRecipeStep',
                                            '\\Model\\KochaboRecipe',
                                        ),
                                    );

    /**
     * Properties, which could be used for filtering
     *
     * @return array
     */
    public function getFilterableByProperties()
    {
        return array(
                    'id' => 'id',
                    'comment' => 'comment',
                    'file_format' => 'file_format',
                    'transparent_background' => 'transparent_background',
                    'size_format' => 'size_format',
                    'original_height' => 'original_height',
                    'original_width' => 'original_width',
                    'original_url' => 'original_url',
                    'small_url' => 'small_url',
                    'medium_url' => 'medium_url',
                    'big_url' => 'big_url',
                    'picturable_id' => 'picturable_id',
                    'picturable_type' => 'picturable_type',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: polymorphic many belongs to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany('\\Model\\Tag', 'tag_attachable', 'tag_attachables');
    }

    /**
     * Relation definition. Type: polymorphic one belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function picturable()
    {
        return $this->morphTo('picturable', 'picturable_type', 'picturable_id');
    }

}
