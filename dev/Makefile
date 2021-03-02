testdir = /tests
phpunit_args = --testdox $(testdir)

test: test-7 test-8

test-7:
	@echo "--------------------------------------------------------------------"
	@echo "Running tests under PHP 7..."
	@docker-compose run web_php7 /usr/bin/phpunit $(phpunit_args)

test-8:
	@echo "--------------------------------------------------------------------"
	@echo "Running tests under PHP 8..."
	@docker-compose run web_php8 /usr/bin/phpunit $(phpunit_args)
