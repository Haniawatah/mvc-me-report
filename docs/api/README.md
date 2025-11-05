# Card Game API Documentation

Welcome to the documentation for the card game project API. This document provides information about available endpoints and how to use them.

## Available Endpoints

You can interact with the card game through these endpoints:

- `/api` - Browse the interactive documentation
- `/api/example` - A simple example endpoint to test the API
- `/api/game` - Get information about the current game state

## How the Documentation Works

I've added PHP DocBlock comments to my controller classes to document the API. These comments follow the OpenAPI/Swagger format, which helps generate this documentation automatically.

## Main Controllers

The API functionality is spread across these controllers:

- `ApiController.php` - Contains the documentation endpoint and basic API functions
- `GameController.php` - Handles all game-related API operations like dealing cards and showing game state

## Using This API

Here's how to get started:

1. Open `/api` in your browser to see the interactive documentation
2. Try out different endpoints directly from the documentation page
3. Look at the example responses to understand what data each endpoint returns

## Maintaining Documentation

I've created a script at `bin/generate-api-docs.php` that processes my DocBlock comments. When I update comments in my controllers, I run this script to refresh the documentation.
