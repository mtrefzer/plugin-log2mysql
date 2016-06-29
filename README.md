## **What is phpMussel?**

An ideal solution for shared hosting environments, where it's often not possible to utilise or install conventional anti-virus protection solutions, phpMussel is a PHP script designed to **detect trojans, viruses, malware and other threats** within files uploaded to your system wherever the script is hooked, based on the signatures of [ClamAV](http://www.clamav.net/) and others.

---

## **What's this repository for?**

This repository, "plugin-log2mysql", is the repository for a phpMussel plugin that allows you to write all logging information to a mysql database.

The core phpMussel repository: [phpMussel](https://github.com/Maikuolan/phpMussel).

---

## **How to install?**

This plugin uses PDO and PDO_MYSQL so this must be enabled on your server.
 
You need a table in your Database with at least the following fields:

```sql
CREATE TABLE {db_name}.{db_table} (
   id INT NOT NULL AUTO_INCREMENT,
   insert_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   origin VARCHAR(50) COMMENT 'IP address of the uploading client',
   objects_scanned INT COMMENT 'Count of the scanned files',
   detections_count INT COMMENT 'Count of detected malware',
   scan_errors INT COMMENT 'Count error while scanning files',
   killdata TEXT COMMENT 'MD5 SIGNATURE RECONSTRUCTION (FILE-HASH:FILE-SIZE:FILE-NAME)',
   detections TEXT COMMENT 'Error messages',
   PRIMARY KEY (id)
)
```

Add the following section to your `phpmussel.ini` file and edit accordingly:

```ini
[log2mysql]
; hostname or IP for the database connection
db_host='127.0.0.1'
; mysql port, Default 3306
db_port=3306
; Name of your database
db_name='your_database'
; Username for the database
db_user='your_database_username'
; Password
db_pass='your_database_userpassword'
; Name of the used table.
db_table='your_tablename'
```

Upload the "log2mysql" directory of this repository and all its contents to the "plugins" directory of your phpMussel installation (the "plugins" directory is a sub-directory of the "vault" directory).

That's everything! :-)

---

*This file, "README.md", last edited: 29th June 2016 (2016.06.29).*
