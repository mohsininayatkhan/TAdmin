Forge installation howto:

1. Checkout repository
2. Change your apache entry point to htdocs/site and ensure Override is allowed for all entities
3. Ensure same user for apache and owner of files, otherwise make sure you have r/w permissions to /tmp, /logs, /htdocs/site/_tpl
4. Use extra/data.dump as MySQL data dump for the framework. Default DB is forge.
5. Go to include/config.php and ensure, that all the settings are fine.
6. Go to include/init.php, and ensure, that all modules you need are there, and those you do not need, are not.
7. Go to htdocs/admin, htdocs/site, htdocs/user and ensure, that .htaccess contains rewrite rules for HTTP or HTTPS as you need
8. Now you can navigate to your YOUR_HOST/admin and you would be able to see login page.
9. In case not, open logs/runtime.log for details.
10. In case when framework is running fine, you can access IGD at YOUR_HOST/_debug
11. Be aware, that due to file organization procedure, if you need any module across entity, you need to create a symlink in approriate folder, otherwise this will produce an error during access to a module.

