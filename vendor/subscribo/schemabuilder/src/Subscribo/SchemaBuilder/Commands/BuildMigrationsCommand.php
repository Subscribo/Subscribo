<?php namespace Subscribo\SchemaBuilder\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Subscribo\Config;
use Fuel\Core\Arr;

use Subscribo\DependencyResolver;
use App;
use View;

class BuildMigrationsCommand extends BuildCommandAbstract {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'build:migrations';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This custom command builds migrations';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $fileName = $this->argument('file');
        $this->info('Building migrations starting. Using file: '. $fileName);
        $this->info('Environment: '. App::environment());
        Config::setForPackage('schemabuilder', 'parsed_schema', array());
        Config::loadFileForPackage('schemabuilder', $fileName, 'parsed_schema', true, null);
        $input = Config::getForPackage('schemabuilder', 'parsed_schema');
        $modelFields = Arr::get($input,'model_fields', array());
        $modelOptions = Arr::get($input, 'model_options', array());
        $pivotTables = Arr::get($input, 'pivot_tables', array());
        $dependencies = $this->_assembleDependencies($modelFields, $modelOptions);
        $migrationBuildOrder = DependencyResolver::resolve($dependencies);
        $modelFields = DependencyResolver::reorder($modelFields, $migrationBuildOrder, true, true);
        $modelOptions = DependencyResolver::reorder($modelOptions, $migrationBuildOrder, true, true);
        $this->_addPivotTables($modelFields, $modelOptions, $pivotTables);
        $dependencies = $this->_assembleDependencies($modelFields, $modelOptions);
        $migrationBuildOrder = DependencyResolver::resolve($dependencies);
        $modelFields = DependencyResolver::reorder($modelFields, $migrationBuildOrder, true, true);
        $modelOptions = DependencyResolver::reorder($modelOptions, $migrationBuildOrder, true, true);

        $this->_buildMigrations($modelFields, $modelOptions, $this::MIGRATIONS_DIR);

        $this->info('Building migrations finished.');
    }


    private function _buildMigrations($modelFields, $modelOptions, $basePath)
    {
        $i = 0;
        foreach ($modelFields as $key => $fields)
        {
            $options = Arr::get($modelOptions, $key, array());
            if ( ! empty($options['generate']['migration']))
            {
                $i++;
                $migrationFileName = sprintf('0_1_0_%04d_create_%s_table.php', $i, $options['table_name']);
                $migrationName = 'Create'.studly_case($options['table_name']).'Table';
                $data = array(
                    'fields' => $fields,
                    'options' => $options,
                    'migrationName' => $migrationName,
                );
                $content = View::make('schemabuilder::commands.build.migration', $data);
                $this->_createFile($basePath.$migrationFileName, $content, $options['generate']['migration']);
            }
        }
    }

    private function _assembleDependencies(array $modelFields, array $modelOptions)
    {
        $dependencies = array();
        foreach($modelFields as $modelKey => $fields) {
            $tableName = $modelOptions[$modelKey]['table_name'];
            $ignoreList = array($tableName);
            $dependencies[$modelKey] = $this->_extractDependenciesFromFields($fields, $modelOptions, $ignoreList);
        }
        return $dependencies;
    }

    private function _extractDependenciesFromFields(array $fields, array $modelOptions, array $ignoreList)
    {
        $result = array();
        foreach ($fields as $fieldKey => $field) {
            if (empty($field['db_foreign_key'])) {
                continue;
            }
            $tableToName = $field['db_foreign_key']['table_to'];
            if (false !== in_array($tableToName, $ignoreList)) {
                continue;
            }
            $dependency = $this->_findKeyByTableName($modelOptions, $tableToName, true);
            $result[] = $dependency;
        }
        return $result;
    }

    private function _addPivotTables(array &$modelFields, array &$modelOptions, array $pivotTables)
    {
        foreach ($pivotTables as $pivotTableKey => $pivotTableOptions)
        {
            $modelOptions[$pivotTableKey] = $pivotTableOptions;
            $modelFields[$pivotTableKey] = $pivotTableOptions['fields'];
        }
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
