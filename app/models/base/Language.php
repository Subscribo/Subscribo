<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model Language
 * Automatically generated abstract model class
 *
 * @method \Model\Language[] get() public static function get(array $columns = array()) returns an array of models Language
 * @method null|\Model\Language first() public static function first(array $columns = array()) returns model Language
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API - language code with country, e.g. DE_AT
 * @property string $englishName
 * @property string $germanName
 * @property string $nativeName
 * @property int $fallbackLanguageId
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\Language|null fallbackLanguage Foreign model related via many belongs to one relation 
 * @property-read \Model\Service[] languagesServices A collection of foreign models related via many to many relation 
 * @property-read \Model\Service[] defaultLanguageServices A collection of foreign models related via has many relation 
 * @property-read \Model\Translation[] translations A collection of foreign models related via has many relation 
 * @property-read \Model\Language[] languages A collection of foreign models related via has many relation 
 */
abstract class Language extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'languages';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'englishName' => array('db_type' => 'varchar'),
                                'germanName' => array('db_type' => 'varchar'),
                                'nativeName' => array('db_type' => 'varchar'),
                                'fallbackLanguageId' => array('db_type' => 'integer'),
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
                                'englishName' => 'english_name',
                                'germanName' => 'german_name',
                                'nativeName' => 'native_name',
                                'fallbackLanguageId' => 'fallback_language_id',
                                'createdAt' => 'created_at',
                                'updatedAt' => 'updated_at',
                            );



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @property array
     */
    protected $hidden = array('defaultLanguageServices');


    /**
     * The attributes included into mass assignment.
     *
     * @var array
     */
    protected $fillable = array(
                                    'identifier',
                                    'english_name',
                                    'german_name',
                                    'native_name',
                                    'fallback_language_id',
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
                                            'languages',
                                        ),

                                        array (
                                            'regex',
                                            '#^[a-zA-Z][a-zA-Z0-9_]*[a-zA-Z0-9]$#',
                                        ),
                                    ),

                                    'english_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'german_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'native_name' => array (
                                        'required',
                                        'max:255',
                                    ),

                                    'fallback_language_id' => array (
                                        'integer',
                                        'between:0,4294967295',
                                        array (
                                            'exists',
                                            'languages',
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

                                    'fallback_language_id' => array (
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
                                        'fallbackLanguage' => '\\Model\\Language',
                                        'languagesServices' => '\\Model\\Service',
                                        'defaultLanguageServices' => '\\Model\\Service',
                                        'translations' => '\\Model\\Translation',
                                        'languages' => '\\Model\\Language',
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
                    'english_name' => 'english_name',
                    'german_name' => 'german_name',
                    'native_name' => 'native_name',
                    'fallback_language_id' => 'fallback_language_id',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many belongs to one
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fallbackLanguage()
    {
        return $this->belongsTo('\\Model\\Language', 'fallback_language_id', null, 'fallbackLanguage');
    }

    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languagesServices()
    {
        return $this->belongsToMany('\\Model\\Service', 'language_service', 'language_id', 'service_id', 'services');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function defaultLanguageServices()
    {
        return $this->hasMany('\\Model\\Service', 'default_language_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany('\\Model\\Translation', 'language_id');
    }

    /**
     * Relation definition. Type: has many
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languages()
    {
        return $this->hasMany('\\Model\\Language', 'fallback_language_id');
    }

}
