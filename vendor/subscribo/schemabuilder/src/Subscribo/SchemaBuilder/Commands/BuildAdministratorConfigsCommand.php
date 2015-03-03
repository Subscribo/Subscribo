<?php namespace Subscribo\SchemaBuilder\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Subscribo\Config;
use Fuel\Core\Arr;

use App;
use View;

class BuildAdministratorConfigsCommand extends BuildCommandAbstract {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'build:administrator-configs';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This custom command builds Frozennode Administrator configuration files';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $fileName = $this->argument('file');
        $this->info('Building Frozennode Administrator configuration files starting. Using file: '. $fileName);
        $this->info('Environment: '. App::environment());
        Config::setForPackage('schemabuilder', 'parsed_schema', array());
        Config::loadFileForPackage('schemabuilder', $fileName, 'parsed_schema', true, null);
        $input = Config::getForPackage('schemabuilder', 'parsed_schema');
        $modelFields = $input['model_fields'];
        $modelOptions = $input['model_options'];

        $this->_buildAdministratorConfigs($modelFields, $modelOptions, $this::CONFIG_DIR.'administrator/');

        $this->info('Build Frozennode Administrator configuration files finished.');
    }


    private function _addOrderedItem(array $arr, $item, $order = null)
    {
        if (empty($order))
        {
            $arr[] = $item;
            return $arr;
        }
        if (array_key_exists($order, $arr))
        {
            throw new \Exception('Order key ('.$order.') already exists.');
        }
        $arr[$order] = $item;
        return $arr;
    }

    private function _buildAdministratorConfigs($modelFields, $modelOptions, $basePath)
    {
        $administratorMainMenu = array();
        $administratorMenu = array();
        foreach ($modelOptions as $key => $options)
        {
            if ( ! empty($options['generate']['administrator'])) {
                $fields = Arr::get($modelFields, $key, array());
                $configName = $options['table_name'];
                $filePath = $basePath.'models/'.$configName.'.php';
                $data = array(
                    'fields' => $fields,
                    'options' => $options,
                    'configuration' => $this->_assembleAdministratorModelConfiguration($fields, $options, $modelFields, $modelOptions),
                );
                $content = View::make('schemabuilder::commands.build.administrator_model_config', $data);
                $this->_createFile($filePath, $content, $options['generate']['administrator']);
                if (true === $options['administrator_menu']) {
                    $administratorMainMenu = $this->_addOrderedItem($administratorMainMenu, $configName, $options['administrator_menu_order']);
                } else {
                    Arr::set($administratorMenu, $options['administrator_menu'],
                        $this->_addOrderedItem(Arr::get($administratorMenu, $options['administrator_menu'], array()), $configName, $options['administrator_menu_order']));
                }
            }
        }
        $listData = array(
            'mainMenu'  => $administratorMainMenu,
            'menu'      => $administratorMenu,
        );
        $listContent = View::make('schemabuilder::commands.build.administrator_model_list', $listData);
        $listFilePath =  $basePath.'model_list.php';
        $this->_createFile($listFilePath, $listContent, 'overwrite');
    }

    private function _assembleAdministratorModelConfiguration($fields, $options, $modelFields, $modelOptions)
    {
        $columns = array();
        $editFields = array();
        $filters = array();
        $foreignObjects = Arr::get($options, 'foreign_objects', array());
        $foreignObjectsForList = $foreignObjects;
        $foreignObjectsForEdit = $foreignObjects;
        foreach ($fields as $field) {
            $connectedObjectKey = $this->_findConnectedObjectKey($field['name'], $foreignObjectsForList);
            if ( ! empty($field['administrator']['list'])) {
                $columns[$field['name']] = array(
                    'title' => $field['title'],
                );
                $connectedObjectConfiguration = null;
                if ($connectedObjectKey) {
                    $connectedObjectConfiguration = $this->_assembleObjectListConfiguration($foreignObjectsForList[$connectedObjectKey], $modelOptions);
                }
                if ($connectedObjectConfiguration) {
                    $columns[$connectedObjectConfiguration['key']] = $connectedObjectConfiguration['configuration'];
                    unset($foreignObjectsForList[$connectedObjectKey]);
                }
            }
            if ( ! empty($field['administrator']['edit'])) {
                $objectSelectConfiguration = null;
                if ($connectedObjectKey) {
                    $objectSelectConfiguration = $this->_assembleObjectSelectConfiguration($foreignObjectsForEdit[$connectedObjectKey], $modelOptions);
                }
                if ($objectSelectConfiguration) {
                    $editFields[$objectSelectConfiguration['key']] = $objectSelectConfiguration['configuration'];
                    unset($foreignObjectsForEdit[$connectedObjectKey]);
                } else {
                    $editFields[$field['name']] = $this->_assembleSimpleEditConfiguration($field);
                }
            }
            if ( ! empty($field['administrator']['filter'])) {
                $filters[$field['name']] = array(
                    'title' => $field['title'],
                    'type'  => $field['administrator']['filter_type'],
                );
                if ('enum' == $field['administrator']['filter_type']) {
                    $filters[$field['name']]['options'] = $field['enum_list'];
                }
                if (('number' == $field['administrator']['filter_type'])
                    and ('decimal' == $field['db_type'])
                    and ( ! empty($field['db_scale']))) {
                    $filters[$field['name']]['decimals'] = $field['db_scale'];
                }
                if ( ! empty($field['description'])) {
                    $filters[$field['name']]['description'] = $field['description'];
                }
            }
        }
        foreach($foreignObjectsForList as $foreignObject) {
            $objectConfiguration = $this->_assembleObjectListConfiguration($foreignObject, $modelOptions);
            if ($objectConfiguration) {
                $columns[$objectConfiguration['key']] = $objectConfiguration['configuration'];
            }
        }
        foreach($foreignObjectsForEdit as $foreignObject) {
            $objectConfiguration = $this->_assembleObjectSelectConfiguration($foreignObject, $modelOptions);
            if ($objectConfiguration) {
                $editFields[$objectConfiguration['key']] = $objectConfiguration['configuration'];
            }
        }
        $result = array(
            'title' => $options['title'],
            'single' => $options['singular'],
            'model' => $options['model_full_name'],
            'columns' => $columns,
            'edit_fields' => $editFields,
            'filters' => $filters,
        );
        return $result;
    }

    private function _assembleSimpleEditConfiguration($field)
    {
        $result = array(
            'title' => $field['title'],
            'type' => $field['administrator']['edit_type'],
        );
        if ('enum' == $field['administrator']['edit_type']) {
            $result['options'] = $field['enum_list'];
        }
        if (('number' == $field['administrator']['edit_type'])
            and ('decimal' == $field['db_type'])
            and (!empty($field['db_scale']))
        ) {
            $result['decimals'] = $field['db_scale'];
        }
        if ( ! empty($field['description'])) {
            $result['description'] = $field['description'];
        }
        if (isset($field['default'])) {
            $result['value'] = $field['default'];
        }
        return $result;
    }

    private function _findConnectedObjectKey($fieldName, $foreignObjects)
    {
        foreach($foreignObjects as $key => $object) {
            $relation = $object['relation'];
            $keyFrom = $this->_obtainKeyFrom($relation, false);
            if ($keyFrom === $fieldName) {
                return $key;
            }
        }
        return null;
    }


    private function _assembleObjectListConfiguration(array $foreignObject, array $modelOptions)
    {
        if ( ! in_array($foreignObject['relation']['type'], array(
            'has_one', 'has_many', 'one_belongs_to_one', 'many_belongs_to_one', 'many_to_many',
            //  'polymorphic_one_has_one', 'polymorphic_one_has_many',
            //  'polymorphic_one_belongs_to_one', 'polymorphic_many_belongs_to_one', 'polymorphic_belongs_to_one',
            //  'polymorphic_many_belongs_to_many',  'many_morphed_by_many',
            //  'has_many_through',
        ), true)) {
            return null;
        }
        $tableToOptions = $this->_findOptionsByTableName($modelOptions, $foreignObject['relation']['table_to'], true);
        $representative = Arr::get($tableToOptions, 'representative');
        if (empty($representative)) {
            return null;
        }
        if ($foreignObject['returns_array']) {
            $select = "GROUP_CONCAT((:table).".$representative.")";
        } else {
            $select = '(:table).'.$representative;
        }
        $title = $this->_humanizeCamelcased($foreignObject['name']);
        $relationship = $foreignObject['name'];
        $configuration = array(
            'title' => $title,
            'relationship' => $relationship,
            'select' => $select,
        );
        $result = array(
            'key' => $relationship,
            'configuration' => $configuration,
        );
        return $result;
    }

    private function _assembleObjectSelectConfiguration(array $foreignObject, array $modelOptions)
    {
        if ( ! in_array($foreignObject['method'], array('belongsTo', 'belongsToMany'), true)) {
            return null;
        }
        $tableToOptions = $this->_findOptionsByTableName($modelOptions, $foreignObject['relation']['table_to'], true);
        $representative = Arr::get($tableToOptions, 'representative');
        if (empty($representative)) {
            return null;
        }
        $title = $this->_humanizeCamelcased($foreignObject['name']);
        $relationship = $foreignObject['name'];
        $configuration = array(
            'title' => $title,
            'type' => 'relationship',
            'name_field' => $representative,
        );
        if (('belongsToMany' === $foreignObject['method'])
            and ( ! empty($foreignObject['relation']['ordering_field_name']))) {
            $configuration['ordering'] = $foreignObject['relation']['ordering_field_name'];
        }
        $result = array(
            'key' => $relationship,
            'configuration' => $configuration,
        );
        return $result;
    }



    private function _humanizeCamelcased($camelCased)
    {
        $title = preg_replace('#([A-Z])#', ' \1', $camelCased); //We first insert a space in front of every capitalized word
        $title = $this->_extraTrim($title); //then remove extra spaces
        $title = strtolower($title);
        $title = ucfirst($title);
        return $title;
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('file', InputArgument::OPTIONAL, 'File used for schema.', $this::SCHEMA_DIR.'parsed_schema.yml'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('stdin', null, InputOption::VALUE_NONE, 'Use stdin instead of file (Not implemented).', null),
		);
	}

}
