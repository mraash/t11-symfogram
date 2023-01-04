phpstan:
	php vendor/bin/phpstan

phpcs:
	php vendor/bin/phpcs

phpcbf:
	php vendor/bin/phpcbf

phpunit:
	php vendor/bin/phpunit

phpunit-curr:
	php vendor/bin/phpunit tests/app/Functional/UserController/SingleTest.php
