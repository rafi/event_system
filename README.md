Event-based Delivery System
---

Database script, execute with MySQL/Percona/MariaDB:
```
CREATE DATABASE event_system CHARACTER SET utf8 COLLATE utf8_general_ci;

USE event_system;

CREATE TABLE comments (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  parent_id bigint(20) unsigned NULL,
  email VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  PRIMARY KEY (id),
  INDEX (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE features (
  id int unsigned NOT NULL AUTO_INCREMENT,
  entity VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  event VARCHAR(80) NOT NULL,
  title VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  INDEX (entity, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO features (entity, name, event, title) VALUES ('comment', 'Smiley', 'submit', 'Smiley replacements');
```

- Run `composer install` for autoloader and phpunit
- Configure database username/password at `app/config/database.php`
- An `.htaccess` at `srv/http` directs all requests to `index.php`
- Run in browser: http://localhost/event_system/srv/http
- Run tests: `bin/phpunit`
