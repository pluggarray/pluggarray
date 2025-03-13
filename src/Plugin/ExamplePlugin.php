<?php
namespace PluggArray\Plugin;


class Context implements PluginInterface
{
    const PATTERN = '/\${(.*?)}/'; // Match ${...}
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(mixed $value, string $path, &$data, callable $next): mixed
    {
        if (is_string($value)) { 
            $value = preg_replace_callback(
                static::PATTERN,
                function ($matches) use ($path) {
                    return $matches[1] . 'at ' . $path . 'has been replaced';
                },
                $value
            );
        }

        return $next($value, $path, $data);
    }
}