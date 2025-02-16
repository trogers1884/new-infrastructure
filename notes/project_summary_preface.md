I am trying to create a base system for creating a comprehensive database for retail sales for a large autogroup.
I am using Laravel and Postgres on a linux server.
My development environment is Laravel 10, Postgres 12, and Ubuntu 22_04.
My production environment will most likely end up being Laravel 10, Postgres 16 and Alma Linux 9.
I'm using PhpStorm for my programming IDE.

We have a basic database policy of creating purposeful schemas in the database and leaving the public schema for base Laravel tables and database objects required by 3rd party library vendors.
We also have a policy that we read from views and write to tables, whenever possible. We also create tables with the preface "tbl_".
