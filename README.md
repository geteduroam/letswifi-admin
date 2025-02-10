# Let's wifi admin

The Lets's wifi admin panel is a frontend panel where Eduroam administrators can manage signed in users and configure realms. 

## Prerequisites

- [Docker](https://docs.docker.com/engine/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

Use `docker compose up -d` to create and build the development environment.

An entry in your hostsfile is still required for things to work. An example entry would look like:

```
127.0.0.1 ibuildings.localhost
```

## Getting started

In order to start the development environment, run `docker compose up -d`. This will build and start the containers that are
used in development to run the application.

# Install dependencies
```
docker compose exec app sh -c 'composer install'

docker compose exec app sh -c 'yarn install'
```

# Install database and migrations

The main database tables can be obtained by downloading 
```
wget https://raw.githubusercontent.com/geteduroam/letswifi-portal/main/sql/letswifi.mysql.sql
```

To then import it from the file into the database use:
```
docker compose cp ./letswifi.mysql.sql database:/

docker compose exec app sh -c 'bin/console --env=dev doctrine:database:drop'

docker compose exec app sh -c 'bin/console --env=dev doctrine:database:create'

docker compose exec database sh -c 'mysql app < ./letswifi.mysql.sql' 
```

After that the migrations can be run by using:
```
docker compose exec app sh -c 'bin/console --env=dev doctrine:migrations:migrate'
```

The application is now up and running and can be accessed at
[https://ibuildings.localhost/](https://ibuildings.localhost). Note that in development the `index.php`
front controller is used automatically, so you don't have to include `/index.php/` in the URLs.

To get started with a default user with all admin rights (user: super@super.nl, password: super) load the following fixture into the contact table:
```
docker compose exec app sh -c 'bin/console --env=dev --append doctrine:fixtures:load'
```

# Security
For development, make sure that the main firewall is active (commented out by
default in security.yaml)
