<?php
namespace Pluggarray;

use PluggArray\Plugin\PluginInterface;

interface PluggArrayInterface
{
    /**
     * Register a plugin
     * 
     * @param PluginInterface|callable $plugin  The plugin to register
     * @param int $priority                     The priority of the plugin
     * 
     * @return static
     */
    public function register(
        PluginInterface|callable $plugin, 
        int $priority = 0
    ): static;

    /**
     * Apply the plugins to the data
     * 
     * @param array $data  The data to plug
     * 
     * @return static
     */
    public function plug(array &$data): static;

    /**
     * Resolve the plugged data
     * 
     * @param array $data  The data to resolve
     * 
     * @return static
     */
    public function resolve(array &$data): static;

}