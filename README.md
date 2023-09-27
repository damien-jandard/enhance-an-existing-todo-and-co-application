# Enhance an existing Todo and Co Application

Project number eight completed as part of my OpenClassrooms training.

[![SymfonyInsight](https://insight.symfony.com/projects/cf3cf695-ca60-4741-8945-a976b338b3ff/big.svg)](https://insight.symfony.com/projects/cf3cf695-ca60-4741-8945-a976b338b3ff)

### Requirements

 * PHP 8.1
 * Symfony CLI
 * Composer 2.3
 
## Install

1. In your terminal, execute the following command to clone the project into the "todolist" directory.
```shell
git clone https://github.com/damien-jandard/enhance-an-existing-todo-and-co-application.git todolist
```

2. Access the "todolist" directory.
```shell
cd todolist
```

3. Duplicate and rename the .env file to .env.local, and modify the necessary information (APP_SECRET, DATABASE_URL, ADMIN_EMAIL).
```shell
cp .env .env.local
```

4. Install the composer dependencies.
```shell
composer install
```

5. Create the database.
```shell
symfony console doctrine:database:create
```

6. Run the migrations.
```shell
symfony console doctrine:migration:migrate --no-interaction
```

8. Adding default fixtures.
```shell
symfony console doctrine:fixtures:load --no-interaction
```

7. Start the local server.
```shell
symfony server:start -d
```

8. You can test the website using the following credentials.

- Administrator Account:
	- Username: admin@replace-me.com
	- Password: Admin1234*

- User Account:
	- Username: user@todolist.com
	- Password: User1234*
