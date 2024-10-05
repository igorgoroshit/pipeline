<?php namespace Igorgoroshit\Pipeline;

class AssetPipeline
{
    /**
     * Parser
     *
     * @var Sprockets\Parser
     */
    private $parser;

    /**
     * Generator
     * @var Sprockets\Generator
     */
    private $generator;

    protected $allowedPaths      = [];
    protected $allowedExtensions = [];
    /**
     * Create the asset repository based on this setup
     *
     * @param unsure atm...
     */
    public function __construct($parser, $generator)
    {
        $this->parser    = $parser;
        $this->generator = $generator;

        $base  = $parser->config['base_path'];
        $paths = $parser->config['paths'];

        $this->allowedPaths = array_map( function($item) use ($base) {
            return "{$base}/{$item}";
        }, $paths);
    }

    public function getAllowedPaths()
    {
        return $this->allowedPaths;
    }
    /**
     * Create javascript include tag(s)
     *
     * @param  string $filename
     * @param  array $attributes
     * @return string
     */
    public function javascriptIncludeTag($filename, $attributes = array(), $version = null)
    {
        $webPaths = array();
        $absolutePaths = $this->parser->javascriptFiles($filename);

        foreach ($absolutePaths as $absolutePath)
        {
            $webPaths[] = $this->parser->absolutePathToWebPath($absolutePath);
        }

        $config = $this->getConfig();
        $composer = $config['javascript_include_tag'];

        return $composer->process($webPaths, $absolutePaths, $attributes, $version);
    }

    /**
     * Create stylesheet link tag(s)
     *
     * @param  string $filename
     * @param  array $attributes
     * @return string
     */
    public function stylesheetLinkTag($filename, $attributes = array(), $version = null)
    {
        $webPaths = array();
        $absolutePaths = $this->parser->stylesheetFiles($filename);

        foreach ($absolutePaths as $absolutePath)
        {
            $webPaths[] = $this->parser->absolutePathToWebPath($absolutePath);
        }

        $config = $this->getConfig();
        $composer = $config['stylesheet_link_tag'];

        return $composer->process($webPaths, $absolutePaths, $attributes, $version);
    }

    /**
     * Create image tag
     *
     * @param  string $filename
     * @param  array $attributes
     * @return string
     */
    public function imageTag($filename, $attributes)
    {
        $absolutePath = $this->file($filename);
        $webPath = $this->parser->absolutePathToWebPath($absolutePath);

        $config = $this->getConfig();
        $composer = $config['image_tag'];

        return $composer->process(array($webPath), array($absolutePath), $attributes);
    }

    /**
     * Is this asset a javascript type?
     *
     * @param  string $filename
     * @return boolean
     */
    public function isJavascript($filename)
    {
        return $this->parser->absoluteJavascriptPath($filename);
    }

    /**
     * Is this filename a stylesheet type?
     *
     * @param  string $filename
     * @return boolean
     */
    public function isStylesheet($filename)
    {
        return $this->parser->absoluteStylesheetPath($filename);
    }

    /**
    * Is this filename a stylesheet type?
    *
    * @param  string $filename
    * @return boolean
    */
    public function isSourcemap($filename)
    {
        //die($filename);
        //die($this->parser->absoluteJavascriptPath($filename));
        //temporary remove map extansion
        //need improvment
        $filename = str_replace('.map', '', $filename);
        $temp = $this->parser->absoluteSourcemapPath($filename);
        //die($temp);
        return $temp;
    }

    protected function allowFile($absolutePath)
    {
        $realFilePath = realpath($absolutePath);
        $info         = pathinfo($realFilePath);
        $allowFile    = false;


        // Check realFilePath
        if(!$realFilePath) {
            return false;
        }

        // Check if the requested file has an extension
        if(!isset($info['extension'])) {
            return false;
        }

        // Check if the requested file starts with a .
        if($info['basename'][0] === '.') {
            return false;
        }

        // Check file exists and it is a file
        if(!file_exists($absolutePath) || !is_file($absolutePath)) {
            return false;
        }

        // Check if the requested file is in one of the allowed paths
        foreach ($this->allowedPaths as $path) {
            if(strpos($realFilePath, $path) === 0) {
                $allowFile = true;
                break;
            }
        }

        if(!$allowFile) {
            return false;
        }

        return true;
    }
    /**
     * Is this filename any type of file?
     *
     * @param  string  $filename
     * @return boolean
     */
    public function isFile($filename)
    {
        $absolutePath = $this->parser->absoluteFilePath($filename);

        return $this->allowFile($absolutePath) ? $absolutePath : null;
    }

    /**
     * Return the javascript associated with this path
     *
     * @param  string $path
     * @return string
     */
    public function javascript($absolutePath)
    {
        return $this->generator->javascript($absolutePath);
    }

    /**
     * Return the stylesheet associated with this path
     *
     * @param  string $absolutePath
     * @return string
     */
    public function stylesheet($absolutePath)
    {
        return $this->generator->stylesheet($absolutePath);
    }

    /**
     * Return the stylesheet associated with this path
     *
     * @param  string $absolutePath
     * @return string
     */
    public function sourcemap($absolutePath)
    {
        return $this->generator->sourcemap($absolutePath);
    }

    /**
     * Return the file download associated with this path
     *
     * @param  string $path
     * @return string | null
     */
    public function file($path)
    {
        return $this->isFile($path);
    }

    /**
     * Get the config array
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->parser->config;
    }

    /**
     * Set the config array
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->parser->config = $config;
        $this->generator->config = $config;
        $this->registerAssetPipelineFilters();
    }

    /**
     * Get the generator
     *
     * @return Sprockets\Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Set the generator
     *
     * @param Sprockets\Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * Get the parser
     *
     * @return Sprockets\Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set the parser
     *
     * @param Sprockets\Parser $parser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;
    }

    /**
     * This calls a method on every filter we have to pass
     * in the current pipeline if that method exists
     *
     * @return void
     */
    public function registerAssetPipelineFilters()
    {
        foreach ($this->parser->config['filters'] as $filters)
        {
            foreach ($filters as $filter)
            {
                if (method_exists($filter, 'setAssetPipeline'))
                {
                    $filter->setAssetPipeline($this);
                }
            }
        }

        foreach ($this->generator->config['filters'] as $filters)
        {
            foreach ($filters as $filter)
            {
                if (method_exists($filter, 'setAssetPipeline'))
                {
                    $filter->setAssetPipeline($this);
                }
            }
        }

        return $this;
    }
}
