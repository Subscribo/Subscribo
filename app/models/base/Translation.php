<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Translation
 * Automatically generated abstract model class
 *
 * @method \Model\Translation[] get() public static function get(array $columns = array()) returns an array of models Translation
 * @method null|\Model\Translation first() public static function first(array $columns = array()) returns model Translation
 *
 * @property int $id Primary key
 * @property int $tableId
 * @property int $fieldId
 * @property int $rowId
 * @property int $languageId
 * @property string $text
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Table|null translationsTable Foreign model related via many belongs to one relation 
 * @property-read \Model\TableField|null field Foreign model related via many belongs to one relation 
 * @property-read \Model\Language|null language Foreign model related via many belongs to one relation 
 */
abstract class Translation extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'translations';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'tableId' => array('db_type' => 'integer'),
                                'fieldId' => array('db_type' => 'integer'),
                                'rowId' => array('db_type' => 'integer'),
                                'languageId' => array('db_type' => 'integer'),
                                'text' => array('db_type' => 'varchar'),
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
                                'tableId' => 'table_id',
                                'fieldId' => 'field_id',
                                'rowId' => 'row_id',
                                'languageId' => 'language_id',
                                'text' => 'text',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'table_id',
                                    'field_id',
                                    'row_id',
                                    'language_id',
                                    'text',
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

                                    'table_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'tables',
                                            'id',
                                        ),
                                    ),

                                    'field_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'table_fields',
                                            'id',
                                        ),
                                    ),

                                    'row_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                    ),

                                    'language_id' => array (
                                        'required',
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'languages',
                                            'id',
                                        ),
                                    ),

                                    'text' => array (
                                        'required',
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

                                    'table_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'field_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'row_id' => array (
                                        'non_printable_to_null',
                                    ),

                                    'language_id' => array (
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
                                        'translationsTable' => '\\Model\\Table',
                                        'field' => '\\Model\\TableField',
                                        'language' => '\\Model\\Language',
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
                    'table_id' => 'table_id',
                    'field_id' => 'field_id',
                    'row_id' => 'row_id',
                    'language_id' => 'language_id',
                    'text' => 'text',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function translationsTable()
    {
        return $this->belongsTo('\\Model\\Table', 'table_id', null, 'table');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo('\\Model\\TableField', 'field_id', null, 'field');
    }

    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('\\Model\\Language', 'language_id', null, 'language');
    }

}
