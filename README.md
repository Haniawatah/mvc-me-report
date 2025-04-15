# MVC Course Report

![MVC Pattern](public/img/mvc-pattern.jpg)

This repository contains my work for the MVC (Model-View-Controller) course at Blekinge Institute of Technology.

## Project Overview

This project is built using the Symfony framework and includes:

- A personal introduction page
- Information about the MVC course
- Report pages for each course assignment
- A "Lucky Number" feature
- A simple JSON API with daily quotes
- Responsive design with consistent layout

## Pages

- `/` - Home page with personal presentation
- `/about` - Information about the MVC course with two-column layout
- `/report` - Course assignment reports (with anchor links like `/report#kmom01`)
- `/lucky` - Dynamic page showing random values and stylized content
- `/api` - Landing page for all available JSON API endpoints
- `/api/quote` - JSON API that returns a random quote of the day with timestamp

## Installation and Setup

Follow these steps to set up the project locally:

1. Clone the repository:
   ```
   git clone https://github.com/Haniawatah/mvc-me-report.git
   ```

2. Navigate to the project directory:
   ```
   cd mvc-me-report
   ```

3. Install dependencies:
   ```
   composer install
   ```

4. Start the local development server:
   ```
   symfony server:start
   ```
   or
   ```
   php -S localhost:8000 -t public/
   ```

5. Visit http://localhost:8000 in your browser

## Project Structure

- `public/` - Web root directory containing public assets
  - `css/` - Stylesheet files
  - `img/` - Image assets
  - `js/` - JavaScript files
- `src/` - PHP source code
  - `Controller/` - Controller classes
  - `Entity/` - Data models
- `templates/` - Twig templates for rendering views
- `config/` - Symfony configuration files

## Design Choices

The website uses a consistent design with:
- A clear header at the top of each page
- A navigation bar for easy site navigation
- A footer with relevant information
- Responsive design using [CSS framework/preprocessor choice]

## JSON API

The project includes a simple JSON API:
- `/api` - Overview of all available API endpoints
- `/api/quote` - Returns a random quote, current date, and generation timestamp

## GitHub Repository

The project is available on GitHub: [https://github.com/Haniawatah/mvc-me-report](https://github.com/Haniawatah/mvc-me-report)

## Version

Current version: 1.0.0

## Author

Created for the MVC course at BTH.