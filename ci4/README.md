# DBMM Rankings on CodeIgniter 4

This is the new CodeIgniter 4 application. The existing CodeIgniter 3 site in
`../public_html` remains untouched while features are migrated incrementally.

## Requirements

- PHP 8.2 or newer
- Composer
- PHP `intl`, `mbstring`, and `mysqli` extensions

## Local setup

1. Copy `.env.example` to `.env`, set the database connection, and set
   `app.baseURL` to `http://localhost:8080/` for local development.
2. Run `composer install` in this directory.
3. Run `composer start` and open `http://localhost:8080`.

The web document root is `public_html`, including when deploying this
application to hosting that requires that directory name.

## Hostinger subdomain deployment

For a subdomain whose document root is `public_html/test`, keep the CI4 project
outside the web root and upload it as a sibling directory named `ci4`. Upload
the **contents** of this project's `public_html` directory (including
`.htaccess` and `index.php`) into `public_html/test`.

The resulting layout should be:

```text
account-root/
├── ci4/                 # app/, vendor/, writable/, .env, spark, composer files
└── public_html/
    └── test/            # CI4 public_html contents; subdomain document root
```

Install Composer dependencies in `account-root/ci4` and place the production
database settings in `account-root/ci4/.env`. Set `app.baseURL` there to
`https://test.dbmm.org.uk/` and keep `CI_ENVIRONMENT = production`. The web
entry point detects this layout while keeping local development working. Since
`.htaccess` rewrites unmatched requests to `index.php`, the CI4 routes work at
the subdomain root.
Keep `app`, `vendor`, `.env`, and `writable` outside `public_html`.

Do not commit `.env`; database credentials belong in that local environment file.
The application is configured to use the `default` MySQLi database group.

All legacy controllers, models, views, language strings, and static assets are
now available through explicit CI4 routes. The existing CI3 controller/model
APIs are temporarily supported by a compatibility layer while features are
incrementally refactored to native CI4 patterns.

The competition rankings page is available at `/rankings/comps/{rar}/{year}`
(`-1/-1` selects all regions and the current ranking period). Existing routes
for players, competitions, armies, matches, login, administration, roster,
rankings, and XML endpoints are also registered. The Glicko calculation route
requires the signed-in user's `Run Glicko` permission; the old command-line-only
Glicko controller is not exposed over HTTP.

For local development, the untracked `.env` should use a database host reachable
from the machine running CI4. The legacy local config files are excluded by the
repository `.gitignore`.
