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

```
