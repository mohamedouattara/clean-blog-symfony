build:
	$(MAKE) prepare-test
	$(MAKE) analyze
	$(MAKE) tests

it:
	$(MAKE) prepare-dev
	$(MAKE) analyze

tests:
	$(MAKE) prepare-test
	php vendor/bin/simple-phpunit

analyze:
	npm audit
	composer valid
	php bin/console doctrine:schema:valid
	php vendor/bin/phpcs

prepare-dev:
	npm install
	npm run dev
	composer install --no-progress --no-suggest --prefer-dist
	php bin/console doctrine:database:drop --if-exists --force --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load --no-interaction --env=dev

prepare-test:
	npm install
	npm run dev
	composer install --no-progress --no-suggest --prefer-dist
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load --no-interaction --env=test