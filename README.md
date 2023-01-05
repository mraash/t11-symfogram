## How to run the project locally

You only need docker for this.

Setup:
1. Clone this repository.
2. Install composer packages by running `docker run --rm -it -v "$(pwd):/app" composer/composer install` command.
3. Run command `docker-compose up -d`.
4. Run migrations by running `docker exec symfogram_web php bin/console doctrine:schema:create` command.
5. Run command `docker-compose down`.
6. Create `./var/testdb/data.sqlite` file.
7. Run migrations for tests by running `docker exec symfogram_web php bin/console --env=test doctrine:database:create` `docker exec symfogram_web php bin/console --env=test doctrine:schema:create` commands.

Run project:
1. Run command `docker-compose up`.

After running the project, you can find it at [http://localhost:5000](http://localhost:5000).

Additional commands you can find in the [Makefile](./Makefile) file.
