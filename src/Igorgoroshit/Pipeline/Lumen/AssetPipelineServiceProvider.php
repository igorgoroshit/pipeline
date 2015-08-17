<?php namespace Igorgoroshit\Pipeline\Lumen;

use Illuminate\Support\ServiceProvider;
use Igorgoroshit\Sprockets\SprocketsParser;
use Igorgoroshit\Sprockets\SprocketsGenerator;
use Igorgoroshit\Pipeline\AssetPipeline;

class AssetPipelineServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
		$this->app->singleton('asset', function($app)
		{
			$config 		= require __DIR__.'/config.php';

			$parser 		= new SprocketsParser($config);
			$generator 	= new SprocketsGenerator($config);
			$pipeline 	= new AssetPipeline($parser, $generator);

			return $pipeline->registerAssetPipelineFilters();
		});

	}


	public function boot()
	{
		include __DIR__.'/routes.php';
	}


}
