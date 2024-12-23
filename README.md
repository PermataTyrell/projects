# Table of Contents

1. [Development Environment](#development-environment)
2. [Tutorial CakePHP](#tutorial-cakephp)

# Development Environment

This application is defined with [Docker Compose](https://docs.docker.com/compose/) V1.
Docker Compose is included in [Docker Desktop](https://docs.docker.com/desktop/).<br/>
Run this command to initiate the Docker environment:
```sh
docker compose up -d
```
**Note:** Technically the command is `docker-compose`. However, for Mac user that uses 
Docker Desktop version 4.23.0 and above, the command might 
not work. Therefore using `docker compose` is the answers. 
Alternatively use the work around suggested [here.](https://github.com/docker/docs/pull/20906) 

## Ports

Docker Compose for development environment exposes following ports:

- 80 (HTTP) http://localhost/
- 3306 (MySQL) username and password are set in [docker-compose.yml](docker-compose.yml)

If another application is using these ports, Docker Compose can't start.
Please stop that application before starting Docker Compose.

## PHP

Docker Compose has `php-app` service.
Use it to run Composer or CakePHP commands.
Commands in Docker Compose run at `/var/www/html` under [app](app) directory.

For example, run following command after `docker compose up -d` to install dependencies:

```sh
docker compose exec php-app composer install
```

### Debugging

The Docker Compose for `php-app` service had been configured with debugging engine Xdebug.<br/>
Please configure the IDE with debugging support for Xdebug by referring to [How to Debug using IntelliJ IDEA](https://redmine2.tyrellsys.com/issues/75611).

### Basic Coding Standard & Quality

To ensure the written code following the basic coding standard & quality, we've configured the Docker Compose for `php-app` service with tools:
1. PHP Code Sniffer (PHPCS) based on CakePHP coding standard rules.
2. PHPStan - PHP Static Analysis Tool.

Everytime making a changes to the code, please run both commands as follows:

```sh
docker compose exec php-app composer check
```

```sh
docker compose exec php-app composer cs-fix
```

If either both tools complaint about errors, then please fix following the basic coding standard & quality.

# Tutorial CakePHP

We'll practice the web development using CakePHP Framework following https://book.cakephp.org/4/en/tutorials-and-examples.html

## Instruction

1. Create a branch named `cms-intro-{name}` under develop branch.
2. All other branches must be created under branch cms-intro-{name}.
3. Please create 1 PR for each topic. For example:

| PR # | Tutorial                                        | Parent Branch      | Working Branch                 | Memo                                                                                                                                                                                                                                                                                                                                                                                                                    |
|:-----|:------------------------------------------------|:-------------------|:-------------------------------|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1    | CMS Tutorial - Creating the Database            | cms-intro-{name}   | cms-database-{name}            | Reference [CMS Tutorial - Creating the Database](https://book.cakephp.org/4/en/tutorials-and-examples/cms/database.html) <br/> - Once created the tables, please refer [CakePHP Migration](https://book.cakephp.org/migrations/3/en/index.html). Please do create initial migration from the existing database.                                                                                                         |
| 2    | CMS Tutorial - Creating the Articles Controller | cms-intro-{name}   | cms-articles-controller-{name} | Reference [CMS Tutorial - Creating the Articles Controller](https://book.cakephp.org/4/en/tutorials-and-examples/cms/articles-controller.html).                                                                                                                                                                                                                                                                         |
| 3    | CMS Tutorial - Tags and Users                   | cms-intro-{name}   | cms-tags-and-users-{name}      | Reference [CMS Tutorial - Tags and Users](https://book.cakephp.org/4/en/tutorials-and-examples/cms/tags-and-users.html)                                                                                                                                                                                                                                                                                                 |
| 4    | CMS Tutorial - Authentication                   | cms-intro-{name}   | cms-authentication-{name}      | Reference [CMS Tutorial - Authentication](https://book.cakephp.org/4/en/tutorials-and-examples/cms/authentication.html)                                                                                                                                                                                                                                                                                                 |
| 5    | CMS Tutorial - Authorization                    | cms-intro-{name}   | cms-authorization-{name}       | Reference [CMS Tutorial - Authorization](https://book.cakephp.org/4/en/tutorials-and-examples/cms/authorization.html)                                                                                                                                                                                                                                                                                                   |

3. Please kindly share the URL of the Pull Request once finish the development.
4. **Important Note:** Please make a small & meaningful commits.