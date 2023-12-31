# MySQL Database Setup

## ON mysql as ROOT:

1. Create database called ProjectDB
```
create database ProjectDB;
```

2. Create admin user
```
create user 'admin'@'localhost' identified by 'adminPass';
```

3. Granting admin user priviledges to the ProjectDB 
```
grant all privileges on ProjectDB.* to 'admin'@'localhost';
```

## ON mysql as ADMIN:
1. Login to admin for MySQL. The password is `adminPass`.  
```
mysql -u admin -p ProjectDB
```

2. Create Users table
```
create table if not exists Users(
    id int not null auto_increment,
    username varchar(30) not null,
    email varchar(255) not null,
    passHash varchar(255) not null,
    salt binary(16),
    primary key(id),
    unique (email),
    unique (username)
);
```
OR

```
create table if not exists Users(id int not null auto_increment, username varchar(30) not null, email varchar(255) not null, passHash varchar(255) not null, primary key(id), unique (email), unique (username));
```


select user, host, authentication_string from mysql.user; //NOT NEEDED

3. Create Review table
```
create table if not exists Reviews (
    reviewId int auto_increment primary key,
    userId int,
    appId int,
    gameRating varchar(10),
    reviewText text,
    foreign key (userId) references Users(id)
);
```

4. Update Users (2FA)
```
alter table Users add column two_factor_code VARCHAR(6), add column code_expiry DATETIME;
```
5. Update steamID
```
alter table Users add column steamID bigint(17) unsigned;
```
6. Update Reviews
```
alter table Reviews add column steamID bigint(17) unsigned, add column hoursPlayed int;
```

## MySQL Commands
### Get Table records
```
select * from table_name;
```

For example, to get User records
```
select * from Users
```

### Get the structure of the Table
```
describe table_name
```

For example, to get the structure of the Users table
```
describe Users;
```

### Delete all records from the Table
```
delete from table_name;
```

For example, to delete all records from the Users table
```
delete from Users;
```

### Remove an existing table
```
drop table table_name;
```

For example, to remove the Users table
```
drop table Users;
```


