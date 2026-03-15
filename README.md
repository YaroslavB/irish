# Irish

[![CI](https://github.com/YaroslavB/irish/actions/workflows/ci.yml/badge.svg)](https://github.com/YaroslavB/irish/actions/workflows/ci.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/YaroslavB/irish/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/YaroslavB/irish/?branch=main)

A Symfony-based web application.

## Requirements

- Docker & Docker Compose (v2.10+)
- Git

## Getting Started

1. Clone the repository:
   ```bash
   git clone git@github.com:YaroslavB/irish.git
   cd irish
   ```

2. Build and start the containers:
   ```bash
   docker compose build --pull --no-cache
   docker compose up -d
   ```

3. Install dependencies:
   ```bash
   docker compose exec php composer install
   ```

4. Run database migrations:
   ```bash
   docker compose exec php bin/console doctrine:migrations:migrate
   ```

5. Open `https://localhost` in your browser and accept the self-signed certificate.

## Useful Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down --remove-orphans

# View logs
docker compose logs -f

# Enter PHP container
docker compose exec php bash

# Clear Symfony cache
docker compose exec php bin/console cache:clear

# Run tests
docker compose exec php bin/phpunit
```

## Development

### Code Quality Tools

```bash
# Run Psalm (static analysis)
docker compose exec php vendor/bin/psalm

# Run Rector (automated refactoring)
docker compose exec php vendor/bin/rector process --dry-run
```

### Database

```bash
# Create a new migration
docker compose exec php bin/console doctrine:migrations:diff

# Run migrations
docker compose exec php bin/console doctrine:migrations:migrate

# Load fixtures
docker compose exec php bin/console doctrine:fixtures:load
```

## Troubleshooting

## Project Structure

```
├── bin/              # Console commands
├── config/           # Configuration files
├── docker/           # Docker configuration
├── migrations/       # Database migrations
├── public/           # Web root
├── src/              # Application source code
│   ├── Command/      # Console commands
│   ├── Controller/   # HTTP controllers
│   ├── Entity/       # Doctrine entities
│   ├── Form/         # Form types
│   ├── Repository/   # Doctrine repositories
│   └── Utils/        # Utility classes
├── templates/        # Twig templates
├── tests/            # Test files
├── translations/     # Translation files
└── var/              # Cache and logs
```

## License

Private repository.

## CI/CD

This project uses GitHub Actions for continuous integration. The following checks run on every push and pull request:

| Workflow | Description |
|----------|-------------|
| **PHP Code Style** | Checks code formatting with PHP-CS-Fixer |
| **Psalm** | Static analysis for type safety |
| **PHPStan** | Additional static analysis |
| **PHPUnit Tests** | Runs the test suite with MySQL and Redis |
| **Security Check** | Scans dependencies for vulnerabilities |
| **Lint** | Validates YAML, Twig, and Doctrine configuration |
| **Rector** | Code quality checks (on PRs only) |

### Running CI locally

```bash
# Run all checks locally before pushing
docker compose exec php vendor/bin/psalm
docker compose exec php vendor/bin/phpstan analyse src --level=5
docker compose exec php bin/phpunit
docker compose exec php bin/console lint:yaml config
docker compose exec php bin/console lint:twig templates
```

