# Black Jack Game - MVC Project

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/build.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/build-status/main)

My final project for the MVC course at BTH - a Black Jack card game built with PHP and Symfony.

## What it does

Play Black Jack against the computer! You can play up to 3 hands at once, split pairs, and the game keeps track of your balance. The dealer follows standard casino rules (stands on 17).

## Getting started

```bash
git clone https://github.com/haniawatah/mvc-me-report.git
cd mvc-me-report
composer install

# Start the server
symfony serve -d
# or just use PHP's built-in server
php -S localhost:8888 -t public
```

Then go to `http://localhost:8888/proj` and start playing!

## Project structure

```
mvc-me-report/
├── src/
│   ├── Blackjack/          # Game logic classes
│   │   ├── Card.php
│   │   ├── Deck.php
│   │   ├── Hand.php
│   │   └── Game.php
│   └── Controller/
│       └── ProjController.php
├── templates/
│   └── proj/               # Game templates
├── tests/
│   └── Blackjack/          # Unit tests (91% coverage)
└── docs/
    ├── api/                # PhpDoc documentation
    ├── metrics/            # PhpMetrics reports
    └── coverage/           # Test coverage HTML report
```

## 🧪 Testing

```bash
# Run all tests
composer phpunit

# Run tests with coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html docs/coverage

# View coverage report
open docs/coverage/index.html
```

**Current Coverage: 91.39%** (84 tests, 224 assertions)

## 📚 Documentation

- **API Documentation:** [docs/api/](docs/api/) - Generated with PhpDoc
- **Code Metrics:** [docs/metrics/](docs/metrics/) - Generated with PhpMetrics
- **Test Coverage:** [docs/coverage/](docs/coverage/) - Generated with PHPUnit

### Generate Documentation

```bash
# PhpDoc
composer phpdoc

# PhpMetrics
composer phpmetrics

# Coverage
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html docs/coverage
```

## 🛠️ Code Quality Tools

```bash
# PHP CS Fixer
composer csfix

# PHPStan (Static Analysis)
composer phpstan

# PHPMD (Mess Detector)
composer phpmd
```

## 🏗️ Technical Implementation

### Object-Oriented Design

- **Card** - Represents a playing card with rank and suit
- **Deck** - Manages 52-card deck with shuffle functionality
- **Hand** - Handles scoring, blackjack detection, and split logic
- **Game** - Main controller for game flow and player management

### MVC Architecture

- **Models:** Game logic in `src/Blackjack/`
- **Views:** Twig templates in `templates/proj/`
- **Controllers:** Route handling in `src/Controller/ProjController.php`

### Session Management

Game state persists across page loads using Symfony sessions, allowing players to resume games.

## 📊 Code Quality Metrics

- **Code Coverage:** 91.39%
- **PHPStan Level:** 8
- **Coding Standards:** PSR-12
- **CI/CD:** Scrutinizer CI

## 📝 Course Information

This project was developed as the final assignment (kmom10) for the course:
- **Course:** DV1608 - Object-Oriented Web Technologies
- **Institution:** Blekinge Institute of Technology (BTH)
- **Program:** Software Engineering

## 🔗 Links

- **Live Demo:** [student.bth.se/~maix24/dbwebb-kurser/mvc/me/report/public/proj](https://www.student.bth.se/~maix24/dbwebb-kurser/mvc/me/report/public/proj)
- **Scrutinizer:** [scrutinizer-ci.com/g/haniawatah/mvc-me-report](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report)

## 📄 License

This project is created for educational purposes as part of the MVC course at BTH.

## 👤 Author

Created by a BTH student for the MVC course final project.