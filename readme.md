# PHP Scripting and Logic Test
### Script Test
#### Dependencies
1. PHP
2. MySQL or XAMPP (MariaDB) 

#### Instructions
1. Download repo whether zipped and unpacked, or use `git clone repo_name https://github.com/jakeb-k/php-task.git`
2. Start MySQL server or XAMPP MySQL option
3. Run the script with `php user-upload.php`
4. Will return the invalid email error
4. To test insert will work use `php user-upload.php --file test.csv` to test with valid entries
5. Feel free to add a duplicate to `test.csv` to test that as well

#### Flags (run with `php user-upload.php`)
- `--file [csv file name]` – this is the name of the CSV to be parsed
- `--create_table` – this will cause the MySQL users table to be built (and no further action will be taken)
- `--dry_run` – this will be used with the --file directive in case we want to run the script but not insert 
into the DB. All other functions will be executed, but the database won't be altered
- `-u` – MySQL username
- `-p` – MySQL password
- `-h` – MySQL host
- `-n` – MySQL schema name
- `--help` – which will output the above list of directives with details.

NOTE: For MySQL variables, ensure that they are inputted in the order they are listed above

### Foobar Test
#### Dependencies
1. PHP

#### Instructions
1. Run `php foobar.php`

#### Output 
1,2,foo,4,bar,foo,7,8,foo,bar,11,foo,13,14,foobar,16,17,foo,19,bar,foo,22,23,foo,bar,26,foo,28,29,foobar,31,32,foo,34,bar,foo,37,38,foo,bar,41,foo,43,44,foobar,46,47,foo,49,bar,foo,52,53,foo,bar,56,foo,58,59,foobar,61,62,foo,64,bar,foo,67,68,foo,bar,71,foo,73,74,foobar,76,77,foo,79,bar,foo,82,83,foo,bar,86,foo,88,89,foobar,91,92,foo,94,bar,foo,97,98,foo,bar,

 