# Manga catalogue

PHP, MySQL, and Twig catalogue of manga titles.

## Configuration

Database settings come from the environment. The source does not contain credentials.

1. Copy `.env.example` to `.env`.
2. Set these variables to your own database details:

| Variable | Purpose |
| --- | --- |
| `DB_HOST` | MySQL server hostname |
| `DB_USER` | MySQL username |
| `DB_PASS` | MySQL password |
| `DB_NAME` | MySQL database name |

You can set the same variables in the web server or shell instead of a file. If a variable is already present in the environment, that value is used and the matching `.env` line is ignored.

`.env` is listed in `.gitignore`. Do not commit it.
