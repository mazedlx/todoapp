# ToDo Demo (Slim 4 + PDO + MySQL)

A small ToDo list demo app. It uses Slim 4 as the framework, PDO for MySQL, sessions for login, SASS/CSS for styles, and a simple SVG logo.

Features
- Login with username/password (default: user / secure)
- No registration; the initial DB setup creates the default user
- Create, edit, delete ToDos and toggle done status
- Automatic DB initialization on first run (database, tables, default user)

Structure (under app/)
- public/ – Webroot (index.php, assets, .htaccess)
- src/ – PHP code (services, views)
- assets/ – styles.scss (SASS source, not publicly served)
- public/assets – styles.css (compiled), logo.svg

Requirements
- PHP >= 8.4 (CLI and web server)
- Composer
- Reachable MySQL server

Configuration via Config.php
- DB_HOST (default: 127.0.0.1)
- DB_PORT (default: 3306)
- DB_NAME (default: todo_demo)
- DB_USER (default: root)
- DB_PASS (default: empty)

Installation
1. Change into app/ and install dependencies:
   composer install
2. Start a server (built-in PHP server):
   composer start
   or
   php -S 0.0.0.0:8080 -t app/public
3. Open in your browser: http://localhost:8080

Login
- Username: user
- Password: secure

SASS/CSS
- Styles are generated from assets/styles.scss (not publicly served).
- Example compilation (with dart-sass):
  sass app/assets/styles.scss app/public/assets/styles.css --style=compressed

Notes
- On first run the database (if not present), tables, and the default user are created automatically.
- For production-like web servers, configure the document root to app/public.

Disclaimer
- This is a demo application and is not intended for any type of production use.
- © thePHP.cc
