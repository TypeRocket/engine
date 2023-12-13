# Register TypeRocket Engine7

1. Install TypeRocket Engine7 with composer.
2. Add your TypeRocket code to the action hook typerocket_engine7_loaded

```bash
composer require typerocket/engine
```

```php
include __DIR__ . '/vendor/autoload.php';

add_action('typerocket_engine7_loaded', function() {
  // Your code here
});
```

*If multiple plugins install TypeRocket Engine7 only the latest version will be loaded.*