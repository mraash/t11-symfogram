phpstan:
	php vendor/bin/phpstan

phpcs:
	php vendor/bin/phpcs

phpcbf:
	php vendor/bin/phpcbf

test-all:
	php vendor/bin/phpunit

test-curr:
	php vendor/bin/phpunit tests/app/Functional/UserController/SingleTest.php
