.PHONY: test clean-deps

test: deps
	phpunit --coverage-text

deps: composer.lock vendor/composer/installed.json

clean-deps:
	rm -rf vendor/

vendor/composer/installed.json: composer.lock
	composer install

composer.lock: composer.json
	composer update
