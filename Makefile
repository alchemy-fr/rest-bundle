VALIDATE = $(shell composer validate 2>&1 | grep -q "lock file" && echo "composer.status")

.PHONY: test clean-deps phpunit phpcs composer.status

test: deps phpunit phpcs

phpunit:
	php -dzend_extension=xdebug.so vendor/bin/phpunit --coverage-text --coverage-clover=build/coverage.clover -v

phpcs:
	php -dzend_extension=xdebug.so vendor/bin/phpcs --standard=psr1,psr2 src

fixcs:
	php -dzend_extension=xdebug.so vendor/bin/phpcbf --standard=psr1,psr2 src

deps: composer.lock vendor/composer/installed.json

vendor/composer/installed.json: composer.json
	composer install

composer.lock: $(VALIDATE)
	composer update

composer.status:
	rm -f composer.lock

ocular:
	[ ! -f ocular.phar ] && wget https://scrutinizer-ci.com/ocular.phar

ifdef OCULAR_TOKEN
scrutinizer: ocular
	@php ocular.phar code-coverage:upload --format=php-clover build/coverage.clover --access-token=$(OCULAR_TOKEN);
else
scrutinizer: ocular
	php ocular.phar code-coverage:upload --format=php-clover build/coverage.clover;
endif

clean-deps:
	rm -rf vendor/

clean:
	rm -rf build/
