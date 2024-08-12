<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Igorgoroshit\Pipeline\Filters\FilterHelper;

class HandlebarsFilter extends FilterHelper implements FilterInterface 
{
    public function __construct($basePath = '/app/assets/javascripts/')
    {
        $this->basePath = $basePath;
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

        $content = str_replace('"', '\\"', $asset->getContent());
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\n", "\\n", $content);
        
        $templateName = "{$parent_dir}{$filename}";

        $emblem  = "try{Ember.TEMPLATES[\"{$templateName}\"] = Ember.Handlebars.compile(\"";
        $emblem .= $content;
        $emblem .= "\");";
        $emblem .= "}\n catch(e) { throw new Error('Template \'{$templateName}\' ' + e.message); }";

        $asset->setContent($emblem);
    }
}
