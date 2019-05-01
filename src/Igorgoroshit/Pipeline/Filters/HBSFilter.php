<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Igorgoroshit\Pipeline\Filters\FilterHelper;

class HBSFilter extends FilterHelper implements FilterInterface 
{
    protected static $v8js = null;
    
    public function __construct($basePath = '/app/assets/javascripts/')
    {
        $this->basePath = $basePath;
        if(!isset(self::$v8js))
        {
            self::$v8js = new \V8Js();
            $pathToCompiler = base_path('app/Assets/vendor/ember-2.12.2/ember-template-compiler.js');
            $compilerSource = file_get_contents($pathToCompiler);
            self::$v8js->executeString("EmberENV = {}; EmberENV['_ENABLE_LEGACY_VIEW_SUPPORT'] = false; window = {}; self = {};\n $compilerSource \n var compiler = Ember.__loader.require('ember-template-compiler');");  
        }
    }

    public function filterLoad(AssetInterface $asset)
    {
    }
 
    public function filterDump(AssetInterface $asset)
    {
        $relativePath = ltrim($this->getRelativePath($this->basePath, $asset->getSourceRoot() . '/'), '/');
        $filename =  pathinfo($asset->getSourcePath(), PATHINFO_FILENAME);
        
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        
        $dirname = explode("templates/", dirname($relativePath . $filename) . '/');
        $parent_dir = ((count($dirname) > 0) ? $dirname[1] : "");

        $content = $asset->getContent();
        
        self::$v8js->templateString = $content;

        $jsCode = 'compiler.precompile(PHP.templateString, false);';

        try {
            $result = self::$v8js->executeString($jsCode);
        }
        catch(\V8JsException $e) {
            throw new \Exception($e->getMessage(), 1);
        }

        $emblem = 'Ember.TEMPLATES["' . $parent_dir . $filename . '"] = Ember.HTMLBars.template(';
        $emblem .= $result;
        $emblem .= ');' . PHP_EOL;

        $asset->setContent($emblem);
    }
}