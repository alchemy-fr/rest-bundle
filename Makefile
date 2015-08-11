VALIDATE = $(shell composer validate 2>&1 | grep -q "lock file" || echo "composer.status")

.PHONY: test clean-deps composer.status

test: deps
	phpunit --coverage-text

deps: composer.lock vendor/composer/installed.json

clean-deps:
	rm -rf vendor/

vendor/composer/installed.json:
	composer install

composer.lock: $(VALIDATE)
	composer update

composer.status:
	rm -f composer.lock
