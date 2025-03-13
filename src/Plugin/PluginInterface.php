<?php
namespace PluggArray\Plugin;

interface PluginInterface
{
    /**
     * @param mixed     $value  The value to be processed
     * @param string    $path   The path of the value
     * @param array     $data   The data reference to pass. Plugins can modify the data.
     * @param callable  $next   The next callable to call
     * 
     * @return mixed
     */
    public function __invoke(
        mixed $value, 
        string $path, 
        array &$data, 
        callable $next
    ): mixed;
}