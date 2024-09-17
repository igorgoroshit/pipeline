<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

use ScssPhp\ScssPhp\Compiler;

class ScssFilter implements FilterInterface
{
	public function filterLoad(AssetInterface $asset)
	{

	}

	public function filterDump(AssetInterface $asset)
	{
		$file = $asset->getSourceRoot() . '/' . $asset->getSourcePath();

		$options = array(
			'style'=>'expanded',
		);

        $scss = file_get_contents($file);

        $compiler = new Compiler();
        $css = $compiler->compileString($scss)->getCss();

		$asset->setContent($css);
	}
}
