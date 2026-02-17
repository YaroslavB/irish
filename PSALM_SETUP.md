# Psalm Setup for Symfony Project

## Current Configuration

The `psalm.xml` file has been configured with default rules for Symfony applications. These rules suppress common false positives that occur when analyzing Symfony code.

## Configured Issue Handlers

### Entity-Specific Rules
- **PropertyNotSetInConstructor**: Suppressed for entities (Doctrine manages properties)
- **MissingConstructor**: Suppressed for entities and forms
- **UnusedProperty**: Suppressed for entities and forms
- **MissingImmutableAnnotation**: Suppressed for entities

### Controller-Specific Rules
- **PossiblyUnusedMethod**: Suppressed for controllers (routes are detected at runtime)
- **PossiblyUnusedParam**: Suppressed for controllers and commands (dependency injection)
- **PossiblyUnusedProperty**: Suppressed for controllers and event listeners

### Repository-Specific Rules
- **MixedInferredReturnType**: Suppressed for repositories (Doctrine query builder)
- **MixedReturnStatement**: Suppressed for repositories

### Service-Specific Rules
- **UnusedClass**: Suppressed for commands, event listeners, and subscribers
- **PossiblyNullArgument**: Suppressed for common controller methods

## Installing Psalm Symfony Plugin (Optional)

For enhanced Symfony support, install the official Psalm Symfony plugin:

### Using Docker:
```bash
docker exec php_irish composer require --dev psalm/plugin-symfony
docker exec php_irish vendor/bin/psalm-plugin enable psalm/plugin-symfony
```

### Without Docker:
```bash
composer require --dev psalm/plugin-symfony
vendor/bin/psalm-plugin enable psalm/plugin-symfony
```

After installation, uncomment the plugin section in `psalm.xml`:

```xml
<plugins>
    <pluginClass class="Psalm\SymfonyPsalmPlugin\Plugin"/>
</plugins>
```

## Running Psalm

### Check all files:
```bash
docker exec php_irish vendor/bin/psalm
```

### Check specific file:
```bash
docker exec php_irish vendor/bin/psalm src/Controller/Admin/UserController.php
```

### Generate baseline (ignore existing issues):
```bash
docker exec php_irish vendor/bin/psalm --set-baseline=psalm-baseline.xml
```

### Show info about issues:
```bash
docker exec php_irish vendor/bin/psalm --show-info=true
```

## Error Levels

Current error level: **2** (Strict mode)

Error levels from most strict to least strict:
- **1**: Most strict - Reports almost everything
- **2**: Strict - Good for new projects
- **3**: Moderate - Balanced
- **4-8**: Progressively less strict

To change error level, modify the `errorLevel` attribute in `psalm.xml`.

## Additional Resources

- [Psalm Documentation](https://psalm.dev/docs/)
- [Psalm Symfony Plugin](https://github.com/psalm/psalm-plugin-symfony)
- [Symfony Best Practices with Psalm](https://psalm.dev/articles/symfony-psalm)

