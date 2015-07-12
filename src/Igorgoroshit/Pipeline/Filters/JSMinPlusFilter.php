<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class JSMinPlusFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {

    }

    public function filterDump(AssetInterface $asset)
    {
		$asset->setContent(\JSMinPlus::minify($asset->getContent()) . ';');
    }
}
