<?php

namespace Model\Base;

use Model\AbstractModel;

/**
 * Model AclRole
 * Automatically generated abstract model class
 *
 * @method \Model\AclRole[] get() public static function get(array $columns = array()) returns an array of models AclRole
 * @method null|\Model\AclRole first() public static function first(array $columns = array()) returns model AclRole
 *
 * @property int $id Primary key
 * @property string $identifier unique string used in API
 * @property string $name human readable name
 * @property string $comment
 * @property string $createdAt Timestamp (datetime) when model was created
 * @property string $updatedAt Timestamp (datetime) when model was last modified
 * @property-read \Model\AclRight[] aclRights A collection of foreign models related via many to many relation 
 * @property-read \Model\AclGroup[] aclGroups A collection of foreign models related via many to many relation 
 */
abstract class AclRole extends \Subscribo\ModelBase\AbstractModel {

    /**
     * The database table used by the model.
     *
     * @property string
     */
    protected $table = 'acl_roles';


    /**
     * All DB properties of the model
     * key - property name, value - array with additional information
     * @var array
     */
    protected $properties = array(
                                'id' => array('db_type' => 'integer'),
                                'identifier' => array('db_type' => 'varchar'),
                                'name' => array('db_type' => 'varchar'),
                                'comment' => array('db_type' => 'varchar'),
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
                                'comment' => 'comment',
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
                                    'comment',
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
                                            'acl_roles',
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

                                    'comment' => array (
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
                                );

    /**
     * Relations available to be used with method with()
     * key - relation method name, value - related model name (string) or an array of names of related models
     *
     * @var array
     */
    protected $availableRelations = array(
                                        'aclRights' => '\\Model\\AclRight',
                                        'aclGroups' => '\\Model\\AclGroup',
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
                    'comment' => 'comment',
                );
    }



    /* Model specific methods follows */


    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function aclRights()
    {
        return $this->belongsToMany('\\Model\\AclRight', 'acl_right_acl_role', 'acl_role_id', 'acl_right_id', 'aclRights');
    }

    /**
     * Relation definition. Type: many to many
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function aclGroups()
    {
        return $this->belongsToMany('\\Model\\AclGroup', 'acl_group_acl_role', 'acl_role_id', 'acl_group_id', 'aclGroups');
    }

}
