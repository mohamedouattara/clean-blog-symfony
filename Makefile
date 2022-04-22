build:
	$(MAKE) prepare-test
	$(MAKE) analyze
	$(MAKE) tests

.PHONY: translations
translations: bin
	php bin/console translation:update --force fr

.PHONY: tests
tests:
	php vendor/bin/simple-phpunit

analyze:
	composer valid
	php bin/console doctrine:schema:valid
	php vendor/bin/phpcs

prepare-dev:
	composer install --no-progress --prefer-dist
	php bin/console doctrine:database:drop --if-exists --force --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load --no-interaction --env=dev

prepare-test:
	composer install --no-progress --prefer-dist
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load --no-interaction --env=test