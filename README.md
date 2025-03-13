# plug
Pluggable array container

## Pluger Class

The `Pluger` class provides a flexible and extensible array container that allows you to plug in additional functionality as needed.

### Features

- **Dynamic Extensions**: Easily extend the functionality of the array container by plugging in new features.
- **Flexible API**: Provides a simple and intuitive API for managing array data.
- **Performance**: Optimized for performance with minimal overhead.

### Installation

To install the `Pluger` class, use Composer:

```bash
composer require exrray/plug
```

### Usage

Here's a basic example of how to use the `Pluger` class:

```php
use Exrray\Plug\Pluger;

// Create a new Pluger instance
$pluger = new Pluger();

// Register plugins
$pluger->register(new SomePlugin());
$pluger->register(new AnotherPlugin());
$pluger->register(
    function ($value, $path, &$data, $next) { 
        $value .'- plugged'; 
        return next($value, $path, $data)
    }
);

// Apply plugins to data
$data = ['key' => 'value'];
$pluger->plug($data);
$pluger->resolve($data);
```

### Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss your ideas.

### License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
