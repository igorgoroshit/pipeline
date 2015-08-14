<?php namespace Igorgoroshit\Pipeline;

use Illuminate\Support\ServiceProvider;
use Igorgoroshit\Sprockets\SprocketsParser;
use Igorgoroshit\Sprockets\SprocketsGenerator;

class LumenAssetPipelineServiceProvider extends ServiceProvider {

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
		$this->package('igorgoroshit/pipeline');

		include_once __DIR__.'/AssetPipelineGlobalHelpers.php';

		$this->app['asset'] = $this->app->share(function($app)
		{
			$config = $app->config->get('pipeline::config');
			$config['base_path'] = base_path();
			$config['environment'] = $app['env'];

			$parser = new SprocketsParser($config);
			$generator = new SprocketsGenerator($config);

			$pipeline = new AssetPipeline($parser, $generator);

			// let other packages hook into pipeline configuration
			$app['events']->fire('pipeline.boot', $pipeline);

			return $pipeline->registerAssetPipelineFilters();
		});

		$this->app['pipeline.setup'] = $this->app->share(function($app)
		{
			return new Commands\PipelineSetupCommand;
		});

		$this->app['pipeline.clean'] = $this->app->share(function($app)
		{
			return new Commands\PipelineCleanCommand;
		});

		$this->app['pipeline.generate'] = $this->app->share(function($app)
		{
			return new Commands\PipelineGenerateCommand;
		});

		$this->commands('pipeline.setup');
		$this->commands('pipeline.clean');
		$this->commands('pipeline.generate');
	}

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		include __DIR__.'/../../routes.php';

		$this->registerBladeExtensions();
	}

	/**
	 * Register custom blade extensions
	 *  - @stylesheets()
	 *  - @javascripts()
	 *
	 * @return void
	 */
	protected function registerBladeExtensions()
	{
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('javascripts');

			return preg_replace($matcher, '$1<?php echo javascript_include_tag$2; ?>', $value);
		});

		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('stylesheets');

			return preg_replace($matcher, '$1<?php echo stylesheet_link_tag$2; ?>', $value);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
