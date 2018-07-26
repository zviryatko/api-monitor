# API monitor

First create database schema:

`./vendor/bin/doctrine orm:schema-tool:create`

Then add few commands
And finally add crontab

`*/5 * * * * php /path/to/api-monitor/bin/console.php monitor:all`
