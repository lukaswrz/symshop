# symshop

A shopping cart API built with Symfony.

## Setup

### Nix and direnv

First off, [install Nix](https://nixos.org/download/).

It is recommended that you configure Nix to avoid possible issues later on.
Specifically, this project uses flakes, which is officially still considered an experimental feature.
Enter this into `~/.config/nix/nix.conf` (create it if it doesn't exist):

```
experimental-features = nix-command flakes
```

Then, [install direnv](https://direnv.net/docs/installation.html).

Restart your shell, re-enter the project directory, and run `direnv allow`, as instructed.
Then, everything should be available in the shell, such as `php`, `composer`, and `devenv`.
In order for this environment to be available in your editor, you either need to start your editor from a shell that has loaded the environment, or if you're using VSCode, it is recommended that you use the official Plugin [`mkhl.direnv`](https://marketplace.visualstudio.com/items?itemName=mkhl.direnv) to dynamically load the environment via direnv.

### Composer

Run `composer install` to install the required PHP dependencies.

### Starting the services

Now that the development shell and Composer dependencies have been set up, run `devenv up` to start the web and database servers.
The application itself will run on [localhost:8000](http://localhost:8000/).
Additionally, an AdminerEvo instance will run on [localhost:8001](http://localhost:8001/).
The credentials for the database can be found in the `.env` file.

### Initializing the database

Run `symfony console doctrine:migrations:migrate` to initialize the database.

### Running the tests

Use the command `php bin/phpunit --testdox` to run all unit tests.

### Create example data

Here are the commands that will generate some example data for you to play around with:

```bash
# Generate 5 users with random data
symfony console app:example user -a 5
# Generate 20 products with random data
symfony console app:example product -a 20
```

### Try the API

The API documentation can be found at [`/api/doc`](http://localhost:8000/api/doc).
API routes are prefixed by `/api/v<version-number>`, e.g. `/api/v1`.

Here are some examples using cURL:

```bash
# Show all users
curl http://localhost:8080/api/v1/users
# Create a user
curl -X POST -H 'Content-Type: application/json' -d '{"email": "some.person@yahoo.com", "firstName": "Some", "lastName": "Person"}' http://localhost:8000/api/v1/users
# Create or replace a user
curl -X PUT -H 'Content-Type: application/json' -d '{"email": "omari.boyle@gmail.com", "firstName": "Omari", "lastName": "Boyle"}' http://localhost:8000/api/v1/users/9
# Delete a user
curl -X DELETE http://localhost:8000/api/v1/users/9
# Add a basket item to a user
curl -X POST -H 'Content-Type: application/json' -d '{"productId": 1}' http://localhost:8000/api/v1/users/2/basket-items
# List all basket items associated with this user
curl http://localhost:8000/api/v1/users/2/basket-items
# Delete a basket item
curl -X DELETE http://localhost:8000/api/v1/users/2/basket-items/1
```

### Making changes

After making changes to the code, it is recommended to run `php-cs-fixer` as follows to alleviate any problems with the code style: `php vendor/bin/php-cs-fixer fix --allow-risky=yes`.

For static analysis, run `php vendor/bin/phpstan analyse`.
