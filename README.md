Symfony 3 Demo
========================

Database
--------------

Create a sqlite database file **"var/data.db3"**.

```
php bin/console doctrine:migrations:migrate
```

Populate the database with data:

```
php bin/console doctrine:fixtures:load
```

---

Server
--------------

```
php bin/console server:start
```

http://127.0.0.1:8000/

---

Tests
--------------

```
composer test
```
