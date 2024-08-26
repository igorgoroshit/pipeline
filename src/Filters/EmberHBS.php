<?php namespace Igorgoroshit\Pipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Igorgoroshit\Pipeline\Filters\FilterHelper;

class EmberHBS extends FilterHelper implements FilterInterface
{

    public function setAssetPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
        $this->parser   = $this->pipeline->getParser();
    }


    public function filterLoad(AssetInterface $asset)
    {
    }
 
    public function filterDump(AssetInterface $asset)
    {
        $base  = $this->parser->get('base_path');
        $paths = $this->pipeline->getParser()->paths();

        $paths = array_map(function($path) use ($base) {
            return "$base/$path";
        }, $paths);

        $src_root = $asset->getSourceRoot();

        $src_suffix = false;
        foreach($paths as $path) {
            if(strpos($src_root, $path) === 0) {
                $src_suffix = substr($src_root, strlen($path)+1);
                break;
            }
        }

        $parts         = explode('/', $src_suffix);
        $template_file = pathinfo($asset->getSourcePath(), PATHINFO_FILENAME);
        $template_name = '';

        //new structure
        if(count($parts) > 2 && $parts[1] == 'js') {

             //[js]/APPNAME/templates
             if($parts[2] == 'templates') {
                $parts = array_slice($parts, 3);
                $template_path = implode('/', $parts);
                $template_name = "{$template_path}/{$template_file}";
             }

             //[js]/APPNAME/components
             else if($parts[2] == 'components') {
                $parts = array_slice($parts, 3);
                $template_path = implode('/', $parts);
                $template_name = "components/{$template_path}";
             }

             //[js]/APPNAME/routes
             else if($parts[2] == 'routes') {
                $parts = array_slice($parts, 3);
                $template_path = implode('/', $parts);
                $template_name = "{$template_path}";                
             }

        }
        //APPNAME/components/[component]/template
        else if (count($parts) > 2 && $parts[1] == 'components') {
            $parts = array_slice($parts, 1);
            $template_path = implode('/', $parts);
            $template_name = "{$template_path}/$template_file";
        }
        else if (count($parts) > 2 && $parts[1] == 'routes') {
            $parts = array_slice($parts, 1);
            $template_path = implode('/', $parts);
            $template_name = "{$template_path}/$template_file";
        }
        else {

            //[javascripts]/APPNAME/templates
            if(count($parts) >= 2 && $parts[1] == 'templates') {
                $parts = array_slice($parts, 2);
            }

            //[javascripts]/templates
            else if(count($parts) >= 1 && $parts[0] == 'templates') {
                $parts = array_slice($parts, 1);
            }

            $template_path = implode('/', $parts);
            $template_name = "{$template_path}/{$template_file}"; 
        }

        $template_name = trim($template_name, '/');

        $content = str_replace('"', '\\"', $asset->getContent());
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\n", "\\n", $content);
        
        $emblem = 'Ember.TEMPLATES["' . $template_name . '"] = Ember.Handlebars.compile("';
        $emblem .= $content;
        $emblem .= '", {moduleName: "'. $template_name. '"});' . PHP_EOL;

        $asset->setContent($emblem);
    }
}
