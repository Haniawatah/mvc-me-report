# Welcome to My MVC Course Journey!

![MVC Pattern](public/img/mvc-pattern.jpg)

Hello there! This repository documents my adventure through the MVC (Model-View-Controller) course at Blekinge Institute of Technology. I'm excited to share my work with you!

## Getting Started

Want to check out this project on your own machine? Follow these steps:

### Clone the Repository

```bash
# Clone the repository
git clone https://github.com/Haniawatah/mvc-me-report.git

# Navigate to the project directory
cd mvc-me-report
```

### Install Dependencies

```bash
# Install all dependencies using Composer
composer install
```

### Configure Environment

```bash
# Create a local environment file if needed
cp .env .env.local

# Edit .env.local to match your environment
```

### Run the Application

```bash
# Start the Symfony development server
symfony server:start

# Or use PHP's built-in server
php -S localhost:8000 -t public/
```

Then open your browser and navigate to `http://localhost:8000`

## What I've Built

Throughout this course, I've created a Symfony-based project that includes:

- A personal page where I introduce myself
- Details about what I'm learning in this MVC course
- Reports for each assignment (documenting my progress and challenges)
- A fun "Lucky Number" feature I built while learning
- My first JSON API delivering daily quotes
- A responsive design that works on all your devices

## Pages You Can Explore

- `/` - Home sweet home! Get to know a bit about me
- `/about` - Learn what this MVC course is all about
- `/report` - Follow my learning journey through assignment reports
- `/lucky` - Try your luck with random numbers (a fun little experiment!)
- `/api` - Gateway to my API creations
- `/api/quote` - Need some inspiration? Get a random quote with timestamp


## How It's Organized

Here's how I've structured everything:

- `public/` - Where all the publicly accessible files live
  - `css/` - Making things pretty
  - `img/` - Pictures and graphics
  - `js/` - Making things interactive
- `src/` - The behind-the-scenes PHP magic
  - `Controller/` - The traffic directors of my application
  - `Entity/` - Data models and structure
- `templates/` - Twig templates that create what you see
- `config/` - Settings and configurations

## My Design Approach

I've created a consistent look and feel with:
- A clean header that helps you know where you are
- An easy-to-use navigation menu
- A footer with useful bits of info
- Responsive design that looks good on phones, tablets, and desktops

## My First API

I've built a simple JSON API:
- `/api` - See all the API endpoints available
- `/api/quote` - Get inspired with a random quote and see when it was generated

## Let's Connect!

Found this helpful or have suggestions? Feel free to reach out! This project represents my learning journey, and I'm always looking to improve.

## GitHub Repository

Check out the code on GitHub: [https://github.com/Haniawatah/mvc-me-report](https://github.com/Haniawatah/mvc-me-report)

## Version

Current version: 1.0.0 (Always evolving as I learn more!)

## About Me

Created with love while learning MVC principles at BTH. This project represents my growth as a developer and my journey into structured web application architecture.