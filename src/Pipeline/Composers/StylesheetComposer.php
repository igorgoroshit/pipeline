<?php namespace Igorgoroshit\Pipeline\Composers;

class StylesheetComposer extends BaseComposer implements ComposerInterface
{
    /**
     * Process the paths that come through the asset pipeline
     *
     * @param  array $paths
     * @param  array $absolutePaths
     * @param  array $attributes
     * @return void
     */
    public function process($paths, $absolutePaths, $attributes, $version = null)
    {
        $url = url('/');
        $attributesAsText = $this->attributesArrayToText($attributes);

        $version = ($version) ? "?v=$version" : "";

        foreach ($paths as $path)
        {
            print "<link href=\"{$url}{$path}{$version}\" {$attributesAsText} rel=\"stylesheet\" type=\"text/css\">" . PHP_EOL;
        }
    }

}