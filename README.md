getAway
==========

First steps when cloned :

```bash
$ ./composer install
```


You will have the following :

```bash
database_driver (pdo_mysql): <Just press enter>
database_host (127.0.0.1): <Just press enter>
database_port (null): <Just press enter>
database_name (symfony): <The name of the just created Database>
database_user (root): <Just press enter if your user is root>
database_password (null): <your password>
mailer_transport (smtp): <Just press enter>
mailer_host (127.0.0.1): <Just press enter>
mailer_user (null): <Just press enter>
mailer_password (null): <Just press enter>
locale (en): <Just press enter>
secret (ThisTokenIsNotSoSecretChangeIt): <Just press enter>
```


To generate the database tables and schemas
execute :
```bash
$ php app/console doctrine:schema:create
$ php app/console doctrine:schema:update --dump-sql
$ php app/console doctrine:schema:update --force
```

After update of the bdd:
```
 [ php app/console doctrine:database:drop --force
 php app/console doctrine:database:create ]
 php app/console doctrine:schema:update --dump-sql
 php app/console doctrine:schema:update --force
```


To test the server on your PC :
execute :
```bash
$ php app/console server:run
```

Pour vider le cache sur le serveur:
```
	./vider-cache
```

If you have issues like
> Failed to write cache file "[your path]/getAway/app/cache/dev/classes.php".

Just use :
```bash
sudo php app/console cache:clear --env=prod
sudo php app/console assets:install --env=prod --symlink
sudo php app/console assetic:dump --env=prod
sudo php app/console cache:warmup --env=prod

php app/console cache:clear
```

For the linux people, write `sudo -u www:data` before each command.
Or use the bash-script `vider-cache`.

HTTPS certificates
------------------

Each year, the certificates need to be update.

Follow the instructions present on the server in the file : /etc/nginx/README.md
