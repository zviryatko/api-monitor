# API monitor

First create database schema:

`./vendor/bin/doctrine orm:schema-tool:create`

Then add few commands
And finally add crontab

```cronexp
*/5 * * * * /path/to/api-monitor/vendor/bin/laminas monitor:all
0 0 * * * /path/to/api-monitor/vendor/bin/laminas monitor:clear
```
