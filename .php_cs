<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude(array('templates', 'tests', 'public'))
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->finder($finder);
