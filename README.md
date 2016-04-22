Symfony 3 Demo
========================

Composer
--------------

Install project dependencies:

```
composer install
```

---

Database
--------------

Create a sqlite database file **"var/data.db3"**:

```
php bin/console doctrine:migrations:migrate
```

Populate the database with data:

```
php bin/console doctrine:fixtures:load
```

---

Assets
--------------

Bower is used to install libraries assets:

```
bower install
```

---

Server
--------------

Run the server:

```
php bin/console server:start
```

To view in the Brower, use `http://127.0.0.1:8000/`

---

Tests
--------------

```
composer test
```
