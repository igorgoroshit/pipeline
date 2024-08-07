<?php namespace Igorgoroshit\Pipeline\Composers;

class JavascriptComposer extends BaseComposer implements ComposerInterface
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
            print "<script src=\"${url}{$path}{$version}\" {$attributesAsText}></script>" . PHP_EOL;
        }
    }
}