reinstall:
	composer update
	php app/console doctrine:database:drop --force
	php app/console doctrine:database:create
	php app/console doctrine:schema:update --dump-sql
	php app/console doctrine:schema:update --force
	echo y | php app/console doctrine:fixtures:load