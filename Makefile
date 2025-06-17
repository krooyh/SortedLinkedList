.PHONY: install
install:
	composer install

.PHONY: fix
fix:
	PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --diff -v src/

.PHONY: test
test:
	./vendor/bin/phpunit

.PHONY: phpstan
phpstan:
	./vendor/bin/phpstan analyse src/ tests/