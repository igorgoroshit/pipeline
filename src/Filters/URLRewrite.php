<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class URLRewrite extends FilterHelper implements FilterInterface
{
    private $baseurl = '/assets';


    public function __construct()
    {

    }

    public function setAssetPipeline($pipeline)
    {
        $config = $pipeline->getConfig();
        $this->allowedPaths = $pipeline->getAllowedPaths();
    }

    public function filterLoad(AssetInterface $asset)
    {
        // do nothing when this is loaded...
    }

    public function filterDump(AssetInterface $asset)
    {
        $this->sourceRoot = $asset->getSourceRoot();
        $content = $asset->getContent();
        $content = $this->filterUrls($content, [$this, 'rewrite']);
        $asset->setContent($content);
    }


    public function rewrite($matches) {

        $query = '';

        //only rewrite relative urls
        if( mb_strpos($matches['url'], '../') !== 0) {
            return $matches[0];
        }

        $parts = parse_url($matches['url']);

        if(isset($parts['query'])) {
            $query = "?{$parts['query']}";
        }

        $path       = "{$this->sourceRoot}/{$parts['path']}";
        $sourceRoot = $this->sourceRoot;
        $realpath   = realpath($path);
        $prefix     = $this->baseurl;

        //only rewrite existing files
        if(!$realpath) {
            return $matches[0];
        }

        foreach($this->allowedPaths as $base) {

            //to make match exect for example npm -> npm-asset vs npm/
            $base .= '/';

            if( strpos($sourceRoot, $base) === 0 ) {

                $newurl = mb_substr($realpath, mb_strlen($base));
                $newurl = "/assets/$newurl";
                return str_replace($matches['url'], $newurl, $matches[0]);
                //"{$prefix}/{$newPath}{$query}";
            }
        }

        return $matches[0];
    }

}
