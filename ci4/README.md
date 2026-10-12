# DBMM Rankings on CodeIgniter 4

This is the new CodeIgniter 4 application. In this repository, the public files
are in `ci4/public_html`, inside the CI4 project. For the Hostinger deployment,
upload the contents of that directory into the account-level `public_html`
document root, alongside the `ci4` project directory. Keep the existing
CodeIgniter 3 site files in a sibling `ci3` directory for migration reference
and rollback preparation.

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

## Hostinger main-domain deployment

To serve CI4 directly from `https://dbmm.org.uk/`, keep the CI4 project outside
the web root in a sibling directory named `ci4`. Before switching, move the
existing CI3 site files into a sibling directory named `ci3` so the old site
can be restored if needed. Keep both application directories outside the web
root so their PHP code and private configuration are not publicly accessible.
Then upload the **contents** of `ci4/public_html` (including `.htaccess` and
`index.php`) to the account-level `public_html` document root. Do not move the
repository's `ci4/public_html` directory out of the CI4 project.

The resulting layout should be:

```text
account-root/
├── ci4/                 # app/, vendor/, writable/, .env, spark, composer files
├── ci3/                 # existing CI3 application and its private config
├── public_html/         # CI4 public_html contents; dbmm.org.uk document root
```

Install Composer dependencies in `account-root/ci4` and place the production
database settings in `account-root/ci4/.env`. Set `app.baseURL` there to
`https://dbmm.org.uk/` and keep `CI_ENVIRONMENT = production`. The web entry
point detects this layout as well as the local-development layout. Since
`.htaccess` rewrites unmatched requests to `index.php`, the CI4 routes work at
the domain root. Keep `app`, `vendor`, `.env`, and `writable` outside
`public_html`.

To roll back, first move the current CI4 public files out of `public_html`, then
move the original CI3 site files from `ci3` back into `public_html`. Keep the
`ci4` directory in place so the deployment can be resumed later.

Do not commit `.env`; database credentials belong in that local environment file.
The application is configured to use the `default` MySQLi database group.

All legacy controllers, models, views, language strings, and static assets are
now available through explicit CI4 routes. The existing CI3 controller/model
APIs are temporarily supported by a compatibility layer while features are
incrementally refactored to native CI4 patterns.

The rankings page uses a native CI4 navigation partial, and language switching
is handled by a native CI4 controller. The player directory route and list now
use a native CI4 controller, model, and view. Player details, results, and match
history also use native CI4 controllers, models, and views. Player Glicko
history is now a native read-only screen; Glicko calculation and editing remain
on the existing compatibility-backed paths. Other player actions continue
using the compatibility layer. The competition directory and its region,
series, and year filters, along with competition details, now use native CI4
controllers, models, and views. The competition match-history display and
match create, edit, and delete workflows are native as well.
The Add Competition and Edit Competition forms and their create, update, and
delete workflows are native. Adding competition series remains on its existing
XML endpoint. Competition results, umpire and stand-in listings, and round
progress are now displayed through a native CI4 controller, model, and view;
individual result creation, editing, and deletion are native. Bulk result
entry, runner registration, and umpire/stand-in management are native. Bulk
CSV replacement currently supports singles competitions.
The active army directory, details, match history, usage statistics, and Top
Dog display are served by native CI4 controllers, models, and views.
The Match Results statistics, Match Details, and Annual Trends screens are
native; CSV export remains on its compatibility-backed route.
The Roster Editor page shell is native and continues to load its existing
client-side scripts; POST requests remain on the compatibility-backed handler.
Login, password submission, authentication, session permission loading, and
logout now use native CI4 controllers and models. The existing client-side MD5
password digest and database format are preserved for compatibility.
The full player list and player create, update, and delete workflows are native,
including region filtering, paging, validation, and a delete confirmation.
The Admin landing dashboard and native user-management forms support user
creation/update, permission assignment, and password change/reset workflows.

The competition rankings page is available at `/rankings/comps/{rar}/{year}`
(`-1/-1` selects all regions and the current ranking period). Existing routes
for players, competitions, armies, matches, login, administration, roster,
rankings, and XML endpoints are also registered. The Glicko leaderboard is
native; its calculation and editing workflows remain on the compatibility
layer. The Glicko calculation route requires the signed-in user's `Run Glicko`
permission; the old command-line-only Glicko controller is not exposed over
HTTP. The Kudos ranking display is native; its ranking calculation continues
to use the compatibility-backed model, and its existing POST behavior remains
on the legacy handler.

## Not yet migrated

The following functionality still uses the CodeIgniter 3-compatible
controllers, models, or workflows:

- **Competition management:** adding competition series remains on the XML
  endpoint.
  Competition directories, details, results, and match-history displays, the
  Add Competition and Edit Competition forms, competition
  create/update/delete workflows, match creation/edit/deletion, runner
  registration, and umpire/stand-in management are native.
- **Rankings workflows:** Kudos score calculation and its POST handler, plus
  Glicko calculation and player-rating editing. The Glicko calculation route
  remains permission-protected.
- **Other endpoints and outputs:** XML API endpoints, match-details CSV
  export, and the legacy Welcome page remain on their existing handlers.
- **Roster Editor:** only the page shell is native. Its existing
  client-side scripts and unfinished editor behavior have not been migrated.
- **Compatibility layer:** legacy controller/model APIs and the remaining
  legacy views are still present to support these paths during migration.

For local development, the untracked `.env` should use a database host reachable
from the machine running CI4. The legacy local config files are excluded by the
repository `.gitignore`.
