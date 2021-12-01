# SQL Structure Loader
## Purpose: Makes migration easy
### Just have your structural/setup .sql files here and run the init_db.php in the browser

- Usage
 - Make sure you prefix the files so they sort in the exact order they should execute
    - Recommend left padding 3 digit numbers (i.e., 001, 002, 003, etc)
 - Make sure the files end in .sql
 - Make sure the path in the require("/path/to/db.php") for init_db.php is correct (it should be if following my project structure)
 - Navigate to the init_db.php in the browser any time you add a new file
