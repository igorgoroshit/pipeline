<?php

/*
|--------------------------------------------------------------------------
| EnvironmentFilter
|--------------------------------------------------------------------------
|
| This is used to run filters on specific environments. For example, if you
| only want to run a filter on production and staging environments
|
| new EnvironmentFilter(new FilterExample, App::environment(), array('production', 'staging')),
|
*/
use Igorgoroshit\Pipeline\Filters\EnvironmentFilter;

return array(

	'base_path' => base_path(),

	'environment' => App::environment(),
	/*
	|--------------------------------------------------------------------------
	| routing array
	|--------------------------------------------------------------------------
	|
	| This is passed to the Route::group and allows us to group and filter the
	| routes for our package
	|
	*/
	'routing' => array(
		'prefix' => '/assets'
	),

	/*
	|--------------------------------------------------------------------------
	| paths
	|--------------------------------------------------------------------------
	|
	| These are the directories we search for files in.
	|
	| NOTE that the '.' in require_tree . is relative to where the manifest file
	| (i.e. app/assets/javascripts/application.js) is located
	|
	*/
	'paths' => array(
		'app/assets/javascripts',
		'app/assets/stylesheets',
		'app/assets/images',
		'lib/assets/javascripts',
		'lib/assets/stylesheets',
		'lib/assets/images',
		'provider/assets/javascripts',
		'provider/assets/stylesheets',
		'provider/assets/images'
	),

	/*
	|--------------------------------------------------------------------------
	| mimes
	|--------------------------------------------------------------------------
	|
	| In order to know which mime type to send back to the server
	| we need to know if it is a javascript or stylesheet type. If
	| the extension is not found below then we just return a regular
	| download.
	|
	*/
	'mimes' => array(
	    'javascripts' => array('.js', '.js.coffee', '.coffee', '.html', '.min.js', '.emb', '.hbs', '.i18n.json'),
	    'stylesheets' => array('.css', '.css.less', '.css.sass', '.css.scss', '.less', '.sass', '.scss', '.min.css'),
			'sourcemaps'	=> array('.js.map', '.css.map', '.min.js.map', '.min.css.map')
	),

	'block-maps-for' => array(
		'vendor.js',
		'vendor.css'
	),

	'sourcemaps' => false,

	/*
	|--------------------------------------------------------------------------
	| filters
	|--------------------------------------------------------------------------
	|
	| In order for a file to be included with sprockets, it needs to be listed
	| here and we can also do any preprocessing on files with the extension if
	| we choose to.
	|
	*/
	'filters' => array(
		'.min.js' => array(

		),
		'.min.css' => array(
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
		),
		'.js' => array(
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.js.coffee' => array(
			//new Igorgoroshit\Pipeline\Filters\CoffeeScript,
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.coffee' => array(
			//new Igorgoroshit\Pipeline\Filters\CoffeeScript,
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.css' => array(
			new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.css.less' => array(
			new Igorgoroshit\Pipeline\Filters\LessphpFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.css.sass' => array(
			new Igorgoroshit\Pipeline\Filters\SassFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.css.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.less' => array(
			new Igorgoroshit\Pipeline\Filters\LessphpFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.sass' => array(
			new Igorgoroshit\Pipeline\Filters\SassFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			//new Igorgoroshit\Pipeline\Filters\URLRewrite(App::make('url')->to('/')),
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\CssMinFilter, App::environment()),
		),
		'.html' => array(
			//new Igorgoroshit\Pipeline\Filters\JST,
			//new EnvironmentFilter(new Igorgoroshit\Pipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.emb' => array(
			new Igorgoroshit\Pipeline\Filters\EmblemFilter
		),
		'.hbs' => array(
			new Igorgoroshit\Pipeline\Filters\HandlebarsFilter
		),
		'.i18n.json' => array(
			new Igorgoroshit\Pipeline\Filters\I18nFilter
		)
	),

	/*
	|--------------------------------------------------------------------------
	| cache
	|--------------------------------------------------------------------------
	|
	| By default we cache assets on production environment permanently. We also cache
	| all files using the `cache_server` driver below but the cache is busted anytime
	| those files are modified. On production we will cache and the only way to bust
	| the cache is to delete files from app/storage/cache/asset-pipeline or run a
	| command php artisan assets:clean -f somefilename.js -f application.css ...
	|
	*/
	'cache' => 	array('production'),

	/*
	|--------------------------------------------------------------------------
	| cache_server
	|--------------------------------------------------------------------------
	|
	| You can create your own CacheInterface if the filesystem cache is not up to
	| your standards. This is for caching asset files on the server-side.
	|
	| Please note that caching is used on **ALL** environments always. This is done
	| to increase performance of the pipeline. Cached files will be busted when the
	| file changes.
	|
	| However, manifest files are regenerated (not cached) when the environment is
	| not found within the 'cache' array. This lets you develop on local and still
	| utilize caching, so you don't have to regenerate all precompiled files while
	| developing on your assets.
	|
	| See more in CacheInterface.php at
	|
	|    https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Cache
	|
	|
	*/
	'cache_server' => new Assetic\Cache\FilesystemCache(storage_path() . '/cache/asset-pipeline'),

	/*
	|--------------------------------------------------------------------------
	| cache_client
	|--------------------------------------------------------------------------
	|
	| If you want to handle 304's and what not, to keep users from refetching
	| your assets and saving your bandwidth you can use a cache_client driver
	| that handles this. This doesn't handle assets on the server-side, use
	| cache_server for that. This only works when the current environment is
	| listed within `cache`
	|
	| Note that this needs to implement the interface
	|
	|	Igorgoroshit\Sprockets\Interfaces\ClientCacheInterface
	|
	| or this won't work correctly. It is a wrapper class around your cache_server
	| driver and also uses the AssetCache class to help access files.
	|
	*/
	'cache_client' => new Igorgoroshit\Pipeline\Filters\ClientCacheFilter,

	/*
	|--------------------------------------------------------------------------
	| concat
	|--------------------------------------------------------------------------
	|
	| This allows us to turn on the asset concatenation for specific
	| environments listed below. You can turn off local environment if
	| you are trying to troubleshoot, but you will likely have better
	| performance if you leave concat on (except if you are doing a lot
	| of minification stuff on each page refresh)
	|
	*/
	'concat' => array('production', 'local'),

	/*
	|--------------------------------------------------------------------------
	| directives
	|--------------------------------------------------------------------------
	|
	| This allows us to turn completely control which directives are used
	| for the sprockets parser that asset pipeline uses to parse manifest files.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'directives' => array(
		'require ' 						=> new Igorgoroshit\Sprockets\Directives\RequireFile,
		'require_directory ' 	=> new Igorgoroshit\Sprockets\Directives\RequireDirectory,
		'require_tree ' 			=> new Igorgoroshit\Sprockets\Directives\RequireTree,
		'require_tree_df ' 		=> new Igorgoroshit\Sprockets\Directives\RequireTreeDf,
		'require_self' 				=> new Igorgoroshit\Sprockets\Directives\RequireSelf,
		'include ' 						=> new Igorgoroshit\Sprockets\Directives\IncludeFile,
		'include_directory ' 	=> new Igorgoroshit\Sprockets\Directives\IncludeDirectory,
		'include_tree ' 			=> new Igorgoroshit\Sprockets\Directives\IncludeTree,
		'stub ' 							=> new Igorgoroshit\Sprockets\Directives\Stub,
		'depend_on ' 					=> new Igorgoroshit\Sprockets\Directives\DependOn,
	),

	/*
	|--------------------------------------------------------------------------
	| javascript_include_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the javascript_include_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'javascript_include_tag' => new Igorgoroshit\Pipeline\Composers\JavascriptComposer,

	/*
	|--------------------------------------------------------------------------
	| stylesheet_link_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the stylesheet_link_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'stylesheet_link_tag' => new Igorgoroshit\Pipeline\Composers\StylesheetComposer,

	/*
	|--------------------------------------------------------------------------
	| image_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the image_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'image_tag' => new Igorgoroshit\Pipeline\Composers\ImageComposer,

	/*
	|--------------------------------------------------------------------------
	| controller_action
	|--------------------------------------------------------------------------
	|
	| Asset pipeline will route all requests through the controller action
	| listed here. This allows us to completely control how the controller
	| should behave for incoming requests for assets.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'controller_action' => '\Igorgoroshit\Pipeline\PipelineController@file',

	/*
	|--------------------------------------------------------------------------
	| sprockets_filter
	|--------------------------------------------------------------------------
	|
	| When concatenation is turned on, when an asset is fetched from the sprockets
	| generator it is filtered through this filter class named below. This allows us
	| to modify the sprockets filter if we need to behave differently.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'sprockets_filter' => '\Igorgoroshit\Sprockets\SprocketsFilter',

	/*
	|--------------------------------------------------------------------------
	| sprockets_filter
	|--------------------------------------------------------------------------
	|
	| When concatenation is turned on, assets are filtered via SprocketsFilter
	| and we can do global filters on the resulting dump file. This would be
	| useful if you wanted to apply a filter to all javascript or stylesheet files
	| like minification. Out of the box we don't have any filters here. Add at
	| your own risk. I don't put minification filters here because the minify
	| doesn't always work perfectly and can bjork your entire concatenated
	| javascript or stylesheet file if it messes up.
	|
	| It is probably safe just to leave this alone unless you are familiar with
	| what is actually going on here.
	|
	*/
	'sprockets_filters' => array(
		'javascripts' => array(),
		'stylesheets' => array(),
		'sourcemaps'	=> array(),
	),

);
