<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Igorgoroshit\Pipeline\Filters\FilterHelper;

class I18nFilter extends FilterHelper implements FilterInterface 
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

        $dirname = explode("translations/", dirname($relativePath . $filename));

        $parts = explode('/', $dirname[1]);
        //$locale = $parts[0];
        $locale = array_shift($parts);
        //array_push($parts);
        $fullpath   = implode('.', $parts);

        $content = str_replace('"', '\\"', $asset->getContent());
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\n", "\\n", $content);

        $json  = 'Em.I18n.setp("'.$locale.'", "' . $fullpath . '", "' . $filename . '", JSON.parse("';
        $json .= $content;
        $json .= '"));/*'.$locale.'*/' . PHP_EOL;

        $asset->setContent($json);
    }
}