VALIDATE = $(shell composer validate 2>&1 | grep -q "lock file" && echo "composer.status")

.PHONY: test clean-deps phpunit phpcs composer.status

test: deps phpunit phpcs

phpunit:
	vendor/bin/phpunit --coverage-text

phpcs:
	vendor/bin/phpcs --standard=psr1,psr2 src

deps: composer.lock vendor/composer/installed.json

vendor/composer/installed.json:
	composer install

composer.lock: $(VALIDATE)
	composer update

composer.status:
	rm -f composer.lock

clean-deps:
	rm -rf vendor/
