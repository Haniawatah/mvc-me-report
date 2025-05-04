# My MVC Course Project

![MVC Pattern](public/img/mvc-pattern.jpg)

## What's this all about?

This is my messy journey through the MVC course at BTH! I'm documenting everything here - the good, the bad, and the "why won't this code work at 3 AM?!"

## Want to check it out?

If you're brave enough to run my code (proceed at your own risk):

```bash
# Grab the repo
git clone https://github.com/Haniawatah/mvc-me-report.git

# Jump into the folder
cd mvc-me-report

# Install all the things
composer install

# Maybe you need to set up some environment stuff
cp .env .env.local
# (Then edit that .env.local file to match your setup)

# Fire it up!
symfony server:start
# (Or use php -S localhost:8000 -t public/ if you're old school)
```

Then point your browser to `http://localhost:8000` and witness my creation!

## Stuff I've built so far

- A homepage with some info about me (nothing too personal, don't worry)
- An about page explaining this course (as if I fully understand it myself)
- Reports page where I document my struggles and occasional victories
- A "Lucky Number" feature that honestly isn't that impressive but hey, it works!
- My first attempts at creating an API (please be gentle)

## Explore at your own risk

- `/` - Just the basic homepage stuff
- `/about` - What is MVC anyway? I try to explain
- `/report` - My learning diary (a.k.a. "What confused me this week")
- `/lucky` - Click for a random number! Revolutionary, I know
- `/api/quote` - Returns a random quote that might inspire you or just make you go "huh?"

## Project structure

I'm trying to keep things organized, but no promises:

- `public/` - The stuff your browser can actually see
- `src/` - Where the real magic (or chaos) happens
- `templates/` - Twig templates that I'm still getting used to
- `config/` - So many settings! Why are there so many settings?!

## My API experiments

Super simple for now:
- `/api/quote` - Gives you a random quote and tells you when it was generated

## Connect with me

If you actually read this far, wow! Thanks! Maybe you want to check out the repo: [https://github.com/Haniawatah/mvc-me-report](https://github.com/Haniawatah/mvc-me-report)

## Version

v0.1 - Just getting started, expect things to break!

## About Me

Student at BTH just trying to understand this whole MVC thing. Send coffee.