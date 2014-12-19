<?php namespace Subscribo\SchemaBuilder;

use Subscribo\ServiceProvider\ServiceProvider;

class SchemaBuilderServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->register('\\Subscribo\\DependencyResolver\\Support\\Laravel\\DependencyResolverServiceProvider');
        $commandSet = array(
            '\\Subscribo\\SchemaBuilder\\Commands\\BuildCommand',
            '\\Subscribo\\SchemaBuilder\\Commands\\BuildSchemaCommand',
            '\\Subscribo\\SchemaBuilder\\Commands\\BuildModelsCommand',
            '\\Subscribo\\SchemaBuilder\\Commands\\BuildMigrationsCommand',
            '\\Subscribo\\SchemaBuilder\\Commands\\BuildAdministratorConfigsCommand',
        );
        $this->commands($commandSet);
    }

    public function boot()
    {
        $this->package('subscribo/schemabuilder');
    }
}
