<?php
namespace PluggArray;

use PluggArray\Plugin\PluginInterface;

/**
 * @package Exrray\Plug
 * @version 1
 * @author Vikor Halytskyi (concept.galitsky@gmail.com)
 * @license MIT
 * 
 * The Pluger class
 * 
 * This class is used to apply plugins to the data.
 * 
 * The Pluger class allows for the dynamic application of various plugins to a given dataset.
 * Plugins are small, self-contained pieces of code that can modify or extend the functionality
 * of the main application. By using the Pluger class, you can easily add, remove, and manage
 * these plugins without altering the core logic of your application.
 * 
 * Key Features:
 * - Add plugins: Dynamically add plugins to the Pluger instance.
 * - Remove plugins: Remove plugins from the Pluger instance.
 * - Apply plugins: Apply all added plugins to the provided data.
 * 
 * Example usage:
 * 
 * $pluger = new Pluger();
 * $pluger->register(new SomePlugin());    // Register a plugin
 * $pluger->register(new AnotherPlugin()); // Register another plugin
 * $pluger->plug($data);                   // Apply the plugins to the data
 * $pluger->resolve($data);                // Resolve the plugged data
 * 
 * This will apply all the added plugins to the $data in the order they were added.
 * 
 */
class PluggArray implements PluggArrayInterface
{
    private array $plugins = [];
    private $callStack = null;

    /**
     * {@inheritDoc}
     */
    public function register(PluginInterface|callable $plugin, int $priority = 0): static
    {
        $this->plugins[$priority][get_class($plugin)] = $plugin;

        $this->callStack = null;

        krsort($this->plugins);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function plug(array &$data): static
    {
        $this->plugNode($data, $data, '');

        return $this;
    }

    /**
     * Plug a node
     * 
     * @param array $node     The node to plug
     * @param array $dataRef  The reference to the data
     * @param string $path    The path of the node
     * 
     * @return static
     */
    protected function plugNode(
        array &$node,
        array &$dataRef,
        ?string $path = null
    ): static
    {
        $pluginStack = $this->getCallStack();

        foreach ($node as $key => &$value) {
            $curPath = (null === $path )? "{$path}.{$key}" : $key;

            if (is_array($value)) {
                $this->plug($value, $dataRef, $curPath);
            } else {

                $value = $pluginStack(
                    $value,
                    $curPath,
                    $dataRef
                );
            }
        }

        return $this;
    }

   /**
    * {@inheritDoc}
    */
    public function resolve(array &$data, ?string $path = null): static
    {
     
        foreach ($data as $key => &$value) {
            $curPath = $path ? "{$path}.{$key}" : $key;

            if (is_array($value)) {
                $this->resolve($value, $curPath);
            } else {
                while (is_callable($value)) {
                    $value = $value();
                }
                if (null === $value) {
                    unset($data[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Get the call stack
     * 
     * @return callable
     */
    protected function getCallStack(): callable
    {
        return $this->callStack ??= $this->callStack();
    }

    /**
     * Get the call stack
     * 
     * @return callable
     */
    protected function callStack(): callable
    {
        $plugins = $this->getSortedPlugins();

        return fn (mixed $value, string $path, array &$dataRef) =>
            function () use ($value, $path, &$dataRef, $plugins) {  
                $next = fn($value, $path, &$dataRef) => $value;
                foreach ($plugins as $plugin) {
                    $prev = $next;
                    $next = fn ($value, $path, &$dataRef) => $plugin($value, $path, $dataRef, $prev);
                }
                return $next($value, $path, $dataRef);
            };
    }

    /**
     * Get the sorted plugins
     * 
     * @return array
     */
    protected function getSortedPlugins(): array
    {
        $sortedPlugins = [];
        foreach ($this->plugins as $priority => $plugins) {
            foreach ($plugins as $plugin) {
                $sortedPlugins[] = $plugin;
            }
        }

        return $sortedPlugins;
    }

}