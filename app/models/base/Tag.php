<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Tag
 * Automatically generated abstract model class
 *
 * @method \Model\Tag[] get() public static function get(array $columns = array()) returns an array of models Tag
 * @method null|\Model\Tag first() public static function first(array $columns = array()) returns model Tag
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name
 * @property int $tagGroupId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\TagGroup|null tagGroup Foreign model related via many belongs to one relation 
 * @property-read \Model\KochaboIngredient[] kochaboIngredients A collection of foreign models related via many morphed by many relation 
 * @property-read \Model\Picture[] pictures A collection of foreign models related via many morphed by many relation 
 */
abstract class Tag extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'tags';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
                                'tagGroupId' => array('db_type' => 'integer'),
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
                                'identifier' => 'identifier',
                                'name' => 'name',
                                'tagGroupId' => 'tag_group_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'identifier',
                                    'name',
                                    'tag_group_id',
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

                                    'identifier' => array (
                                        'required',
                                        'max:255',
                                        array (
                                            'unique',
                                            'tags',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'tag_group_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'tag_groups',
                                            'id',
                                        ),
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

                                    'tag_group_id' => array (
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
                                        'tagGroup' => '\\Model\\TagGroup',
                                        'kochaboIngredients' => '\\Model\\KochaboIngredient',
                                        'pictures' => '\\Model\\Picture',
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
                    'identifier' => 'identifier',
                    'name' => 'name',
                    'tag_group_id' => 'tag_group_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tagGroup()
    {
        return $this->belongsTo('\\Model\\TagGroup', 'tag_group_id', null, 'tagGroup');
    }

    /**
     * Relation definition. Type: many morphed by many
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function kochaboIngredients()
    {
        return $this->morphedByMany('\\Model\\KochaboIngredient', 'tag_attachable', 'tag_attachables');
    }

    /**
     * Relation definition. Type: many morphed by many
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function pictures()
    {
        return $this->morphedByMany('\\Model\\Picture', 'tag_attachable', 'tag_attachables');
    }

}
