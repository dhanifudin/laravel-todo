# OpenAPI Integration Plan (using dedoc/scramble)

This document outlines the plan to integrate the `dedoc/scramble` package to automatically generate OpenAPI documentation for the project.

## 1. Installation

Install the package using Composer:

```bash
composer require dedoc/scramble
```

## 2. Configuration (Optional)

Publish the package's configuration file to customize settings like the API path and description:

```bash
php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider" --tag="scramble-config"
```

## 3. Accessing Documentation

`dedoc/scramble` automatically generates the OpenAPI documentation from your existing API routes and controllers.

- **UI Documentation**: The documentation UI will be available at `/docs/api`.
- **JSON Specification**: The raw OpenAPI JSON file will be available at `/docs/api.json`.

No manual annotations are required for the basic setup. The package will inspect the code to generate the documentation.
