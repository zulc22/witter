# witter
A 2009 recreation of old Twitter. Code needs refactoring

## How to Setup
### Requirements
Apache2/nginx

MySQL

PHP7.2+
### Steps
`git clone https://github.com/the-real-sumsome/witter.git`

Move git cloned files to `/var/www/html` or wherever your webroot is located

copy `/static/example_config.inc.php` to `/static/config.inc.php` and edit it
(config.inc.php is protected by gitignore to prevent credential leaks)

Get a Recaptcha key at https://www.google.com/recaptcha/admin/create

Import the SQL file in the repository
