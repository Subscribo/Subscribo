<?php namespace Subscribo\SchemaBuilder\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App;

class BuildCommand extends BuildCommandAbstract {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'build';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This custom command builds the whole scaffolding';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        if ($this->option('publish')) {
            $this->call('vendor:publish', array('--tag' => 'modelschema', '--force' => true));
        }

        $inputFile = $this->argument('input_file');
        $outputFile = $this->argument('output_file');

        $this->info('Complete Build starting.');
        $this->info('For input schema using file: '. $inputFile);
        $this->info('For processed schema using file: '. $outputFile);
        $this->info('Environment: '. App::environment());

        $this->call('build:schema', array($inputFile, $outputFile));
        $this->call('build:models', array($outputFile));
        $this->call('build:migrations', array($outputFile));
    //    $this->call('build:administrator-configs', array($outputFile));
/*
        $exitCode = null;
        passthru('php ~/bin/composer.phar dump-autoload -o -vvv', $exitCode);
        $this->comment('Exit code for composer dump-autoload: '.$exitCode);
*/
     //   $this->call('dump-autoload');
        $this->info('Complete build finished.');
  //      return $exitCode;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('input_file', InputArgument::OPTIONAL, 'File used for input schema.', self::SCHEMA_DIR.'schema.yml'),
            array('output_file', InputArgument::OPTIONAL, 'File used for output schema.', self::SCHEMA_DIR.'parsed_schema.yml'),
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
            array('publish', null, InputOption::VALUE_NONE, 'Before running force publish schema files from packages', null)
		);
	}

}
