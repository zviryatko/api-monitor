# API monitor

### Tiny, simple, self-hosted api monitoring tool

![Screenshot from 2024-07-03 11-00-55](https://github.com/zviryatko/api-monitor/assets/1087411/a81db206-fc89-4ca6-a131-bef4e13a85e5)

## Requirements

- PHP: ^8.2
- Mysql/Mariadb
- Crontab
- curl

## Setup

`composer install`

Provide then ext env vars:

```env
# Env prod/dev
ENV=prod

# Database credentials, driver
DB_HOST=
DB_USER=
DB_PASSWORD=
DB_NAME=
DB_DRIVER=pdo_mysql

# Auth0 used for registration/login.
AUTH0_DOMAIN=
AUTH0_CLIENT_ID=
AUTH0_CLIENT_SECRET=
AUTH0_COOKIE_SECRET=
```

Create database schema:

`./vendor/bin/doctrine orm:schema-tool:create`

Then open in browser, login and add project and jobs.
And finally add crontab.

```cronexp
*/5 * * * * /path/to/api-monitor/vendor/bin/laminas monitor:all
0 0 * * * /path/to/api-monitor/vendor/bin/laminas monitor:clear
```
