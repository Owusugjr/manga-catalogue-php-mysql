# Manga catalogue

A small PHP and MySQL web app for browsing and managing a catalogue of manga. Visitors can browse the highest-rated titles, open a detail page, and search by title, genre, and year. Registered users can add, edit, and delete entries.

**Status:** Completed university module project (5CS045, 2025).

## Tech stack

- **PHP** with the `mysqli` and session extensions. Pages are plain PHP scripts, one per action.
- **MySQL** (or MariaDB) for the `users` and `manga` tables.
- **Twig** for HTML templates. Bootstrap is not bundled; each template loads **Bootstrap 5.3.1** from the jsDelivr CDN.
- **Composer** is required to install Twig. This repository does not include `composer.json` or `vendor/`.
- Database settings come from environment variables, with an optional local `.env` file. See [`.env.example`](.env.example).

## Features

- **Registration.** `register.php` checks the username length, requires matching passwords of at least 6 characters, and asks for a simple addition CAPTCHA stored in the session. Usernames are checked for duplicates with a prepared statement. Passwords are stored with `password_hash()` (`PASSWORD_DEFAULT`), and the account row is inserted with a second prepared statement.
- **Login, session, and logout.** `login.php` looks the user up with a prepared statement and checks the password with `password_verify()`. It uses the same style of addition CAPTCHA. A successful login stores `user_id` and `username` in the session and redirects to the home page. `logout.php` clears the session and returns home. The navbar shows the current username, or Login and Register links when nobody is signed in.
- **Home page.** `index.php` lists the top 5 rows from `manga`, ordered by `google_rating` descending, with links through to each detail page.
- **List and detail.** `game.php` lists every manga title ordered by release year. `manga-details.php` shows title, genre, release year, Google rating, and description for a numeric id.
- **Search.** `search.php` filters by title, genre, and year. Blank fields are ignored. Title and genre use a `LIKE` match; year has to be numeric. The title box asks `ajax-title.php` for suggestions: a JSON array of up to 10 distinct titles once the query is at least 2 characters, filled in with `fetch()`.
- **Add, edit, and delete for signed-in users.** `manga-add.php`, `manga-edit.php`, and `manga-delete.php` redirect to login when `$_SESSION['user_id']` is missing. Add and edit validate title, genre, year, and Google rating before writing. The list page shows Edit and Delete only when someone is logged in; delete asks for a browser `confirm()` first.

## Setup

You need PHP (8.x is a good fit), MySQL or MariaDB, and Composer. The app expects two tables. This repo does not ship a schema, so create them yourself with the columns the queries use:

| Table | Columns the code reads and writes |
| --- | --- |
| `users` | `id`, `username`, `password_hash` |
| `manga` | `id`, `title`, `release_year`, `description`, `google_rating`, `genre` |

`id` is the key used in links. Pick types that match the values: whole numbers for ids and `release_year`, a short string for `username` and `genre`, a longer string for `title` and `description`, a decimal for `google_rating`, and a string long enough for a `password_hash()` result.

Twig is loaded from the **parent** of this project, not from a `vendor/` folder inside it:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

From the directory that contains this project folder:

```bash
composer require twig/twig
```

That produces `../vendor/autoload.php`, which is the file the scripts include.

Copy the example environment file and fill in a database you control:

```bash
cp .env.example .env
```

| Variable | Purpose |
| --- | --- |
| `DB_HOST` | MySQL server hostname |
| `DB_USER` | MySQL username |
| `DB_PASS` | MySQL password |
| `DB_NAME` | MySQL database name |

`db.php` reads those names from the process environment first. If one is missing, it uses the matching line from a `.env` file in this directory. `.env` is listed in `.gitignore` and is not part of the history. Do not commit real credentials.

Point a PHP server at this directory. The built-in server is enough for local use:

```bash
php -S localhost:8000
```

Then open `http://localhost:8000/index.php`.

## Project structure

```
index.php            Home page: top 5 manga by Google rating
game.php             Full catalogue list (template: templates/games.html)
manga-details.php    One title
search.php           Title, genre, and year search
ajax-title.php       JSON title suggestions for the search box
register.php         Create an account
login.php            Sign in
logout.php           End the session
manga-add.php        Insert a title (login required)
manga-edit.php       Update a title (login required)
manga-delete.php     Delete a title (login required)
db.php               mysqli connection from environment variables
.env.example         Placeholder database settings
templates/           Twig views, including the shared navbar
```

`game.php` and `templates/games.html` still use the word "games" from an earlier version of the project. They list manga.

## Screenshots

Screenshots are not in the repository yet. Add them here later: the home page with the top-rated table, the search page with title suggestions open, a detail page, and the add form while signed in.

## What I learned

This module was where the pieces of a small dynamic site stopped being separate exercises. Registration and login use `password_hash()` and `password_verify()` so the database never stores the password itself, and those two queries use prepared statements. Sessions carry the login from page to page, and a CAPTCHA sits on both forms. Twig keeps the HTML out of the query scripts, and Bootstrap supplies the layout without a custom CSS framework. The search suggestions are a small `fetch()` call against a JSON endpoint, which was the first time I wired the browser and PHP together without a full page reload. The last pass moved the database settings out of the source and into environment variables, with `.env.example` as the only committed copy.

It also showed the difference between a query that is safe and one that only looks safe. Escaping strings and casting numbers is not the same as a prepared statement, and a delete link is not an authorization check.

## Known limitations / next steps

- **Escaping instead of prepared statements.** Registration and login use prepared statements. Search, title suggestions, add, and edit build SQL with `mysqli_real_escape_string()` for text and casts for numbers. Detail, edit, and delete interpolate the id only after checking it is an integer. Those writes and searches should use prepared statements too.
- **Delete is a GET link.** `manga-delete.php` runs as soon as a logged-in user opens `manga-delete.php?id=...`. The list page only adds a JavaScript `confirm()`. There is no CSRF token, and there is no ownership check: any signed-in user can delete any row. Delete should be a POST with a CSRF token, and rows should record who created them if only that person may change them.
- **No SQL schema file yet.** Table and column names are implied by the queries. A new machine still needs the tables created by hand.
- **`vendor/` is not in the repo.** There is no `composer.json` either. Twig is loaded from `../vendor/autoload.php`, one directory above the project, while `.gitignore` ignores `vendor/` here. A proper setup would add `composer.json` in this project and point the `require_once` at `__DIR__ . '/vendor/autoload.php'`.
- **`login.php` does not parse.** The `POST` handler opened near the top of the file is never closed, so PHP rejects the script before the login logic can run. The prepared statement, `password_verify()`, session writes, and CAPTCHA are in the file; they need that brace fixed before anyone can sign in.
- **Search does not start a session.** `search.php` reads `$_SESSION` for the navbar without calling `session_start()`, so the signed-in state on that page is unreliable.
- **The full-list rating column uses the wrong field.** `templates/games.html` prints `item.rating`. The other pages, and the `INSERT`/`UPDATE` statements, use `google_rating`, so the rating cell on the all-manga table does not show the stored Google rating.
