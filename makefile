.PHONY: stan
stan:
	php vendor/bin/phpstan --memory-limit=256M analyse --configuration=phpstan.neon --level=9

.PHONY: pint
pint:
	php vendor/bin/pint

.PHONY: test
test:
	php artisan test
	php vendor/bin/pest --type-coverage --min=100

