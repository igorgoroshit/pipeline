<?php namespace Igorgoroshit\Pipeline\Composers;

interface ComposerInterface
{
    public function process($paths, $absolutePaths, $attributes);
}