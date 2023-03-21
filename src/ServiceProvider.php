<?php

namespace VV\AnchorNavigation;

use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        Tags\AnchorNavigation::class,
    ];

    public function bootAddon()
    {
        $this->bootAddonConfig();

        Augmentor::addExtension('heading', new Nodes\Heading());
    }

    protected function bootAddonConfig(): self
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/anchor-navigation.php', 'anchor-navigation');

        $this->publishes([
            __DIR__ . '/../config/anchor-navigation.php' => config_path('anchor-navigation.php'),
        ], 'anchor-navigation-config');

        return $this;
    }
}
