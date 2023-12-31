# Database Replication Guide

## Step 1 — Prerequisite

### 1.1 Setup the Networking for the <span style="color:red">Source</span> and <span style="color:green">Replica</span> VMs

1. Go to VM settings 
2. Go to Network
3. Go to Adapter 2 tab
4. Enable the Network Adapter
5. Set the `Attached to` field to `Host-only Adapter`

It should look like this:

![vm_network_configuration_for_db_replication](./../images/vm_network_configuration_for_db_replication.PNG)

### 1.2 Install SSH on the <span style="color:red">Source</span> and <span style="color:green">Replica</span> VMs

1. Install openssh server

    ```
    sudo apt install openssh-server
    ```
  

2. Enable the ssh service by typing:

    ```
    sudo systemctl enable ssh
    ```

3. Start the ssh service by typing:

    ```
    sudo systemctl start ssh
    ```


## Step 2 — Adjusting Your Source Server’s Firewall

The command allows any connections that originate from the replica server’s IP address:

<span style="color:red">Source</span> VM:

```
sudo ufw allow from replica_server_ip_address to any port 3306
sudo ufw allow from replica_server_ip_address to any port 22
```

### 2.1 Note for <span style="color:red">Source</span> and <span style="color:green">Replica</span> VM IP Addresses
Use the Adapter IP address for the <span style="color:red">Source</span> and <span style="color:green">Replica</span> VMs. The Adapter IP address should start with 192.

For example, these are my <span style="color:red">Source</span> and <span style="color:green">Replica</span> VM IP addresses:

<span style="color:red">Source</span> VM IP Address: 192.168.56.101 \
<span style="color:green">Replica</span> VM IP Address: 192.168.56.105

You can find this information with `ip addr` at `enp0s8` interface.

## Step 3 — Configuring the Source Database

Make sure to be on the root of the repository

For my case:

<span style="color:red">Source</span> VM:

```
cd ~/git/rabbitmq
```

Copy the source mysqld config file, `mysql_configs/source/mysqld.cnf`, to /etc/mysql/mysql.conf.d/mysqld.cnf

```
sudo cp mysql_configs/source/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
```

Open the MySQL server configuration file on the **source server**.  

```
sudo vim /etc/mysql/mysql.conf.d/mysqld.cnf
```
  
Replace `127.0.0.1` with the source server’s IP address:

File: /etc/mysql/mysql.conf.d/mysqld.cnf

```
. . .
bind-address            = source_server_ip
. . .
```

After making these changes, save and close the file.

Then restart the MySQL service by running the following command:

```
sudo systemctl restart mysql
```

## Step 4 — Creating a Replication User

Login to mysql as root

<span style="color:red">Source</span> VM:

```
sudo mysql -u root
```
  

From the prompt, create a new MySQL user. In this case, you will create a user named replica_admin. Be sure to change `replica_server_ip` to your replica server’s public IP address and change its `password`:

```
CREATE USER 'replica_admin'@'replica_server_ip' IDENTIFIED WITH mysql_native_password BY 'adminPass';
```
  

After creating the new user, grant them the appropriate privileges. At minimum, a MySQL replication user must have the `REPLICATION SLAVE` permissions:

```
GRANT REPLICATION SLAVE ON *.* TO 'replica_admin'@'replica_server_ip';
```
  

Following this, it’s good practice to run the `FLUSH PRIVILEGES` command. This will free up any memory that the server cached as a result of the preceding `CREATE USER` and `GRANT` statements:

```
FLUSH PRIVILEGES;
```

## Step 5 — Retrieving Binary Log Coordinates from the Source

From the prompt, run the following command which will close all the open tables in every database on your source instance and lock them:

<span style="color:red">Source</span> VM:

```
FLUSH TABLES WITH READ LOCK;
``` 
  

Then run the following operation which will return the current status information for the source’s binary log files:

```
SHOW MASTER STATUS;
```
  
If your source MySQL instance is a new installation or doesn’t have any existing data you want to migrate to your replicas, you can at this point unlock the tables:

```
UNLOCK TABLES;
```
  
### 5.1 If Your Source Has Existing Data to Migrate  

From the new terminal window or tab, open up another SSH session to the server hosting your source MySQL instance:

<span style="color:green">Replica</span> VM:

```
ssh username@source_server_ip
```
  

Then, from the new tab or window, export your database using `mysqldump`. The following example creates a dump file named `ProjectDB.sql` from a database named `ProjectDB`. Also, be sure to run this command in the bash shell, not the MySQL shell:

```
sudo mysqldump -u root ProjectDB > ProjectDB.sql
```
  

Return to the terminal that should still have the MySQL shell open. From the MySQL prompt, unlock the databases to make them writable again:

<span style="color:red">Source</span> VM:

```
UNLOCK TABLES;
```

Then you can exit the MySQL shell:

```
exit
```
  
Make sure to be on the home directory
```
cd ~
```

You can now send your snapshot file to your replica server. You can do this securely with the `scp` command like this:

```
scp ProjectDB.sql username@replica_server_ip:/tmp/
```
  

Be sure to replace `username` with the name of the administrative Ubuntu user profile you created on your replica server, and to replace `replica_server_ip` with the replica server’s IP address. Also, note that this command places the snapshot in the replica server’s `/tmp/` directory.

  

After sending the snapshot to the replica server, SSH into it:

```
ssh username@replica_server_ip
```  
  

Then open up the MySQL shell:

```
sudo mysql -u root
```
  

From the prompt, create the new database that you will be replicating from the source:

```
CREATE DATABASE ProjectDB;
```

If the ProjectDB database exist, delete this database and create the database:

```
DROP DATABASE ProjectDB;
CREATE DATABASE ProjectDB;
```
  

You don’t need to create any tables or load this database with any sample data. That will all be taken care of when you import the database using the snapshot you just created. Instead, exit the MySQL shell:

```
exit
```


Then import the database snapshot:

```
sudo mysql ProjectDB < /tmp/ProjectDB.sql
```
  

## Step 6 — Configuring the Replica Database

All that’s left to do is to change the replica’s configuration similar to how you changed the source’s configuration.

Copy the replica mysql config file, `mysql_configs/replica/mysqld.cnf`, to /etc/mysql/mysql.conf.d/mysqld.cnf, this time on your **replica server**:

<span style="color:green">Replica</span> VM:

```
sudo cp mysql_configs/replica/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
```

**Important Note**: Open the mysql config file, `mysqld.cnf`, at /etc/mysql/mysql.conf.d/mysqld.cnf. The config sets sync_binlog to 1 to avoid synchronous issues: 

File: /etc/mysql/mysql.conf.d/mysqld.cnf

```
. . .
sync_binlog = 1
```

Restart MySQL on the replica to implement the new configuration:

```
sudo systemctl restart mysql
```

After restarting the `mysql` service, you’re finally ready to start replicating data from your source database.

## Step 7 — Starting and Testing Replication

At this point, both of your MySQL instances are fully configured to allow replication. We get start getting with data replication from the source. 

In the sql_queries/configure_replica.sql file. Replace the source_server_ip to the IP address of the source VM on your **replica server**:

<span style="color:green">Replica</span> VM:

File: sql_queries/configure_replica.sql
```
. . .
SOURCE_HOST='source_server_ip',
. . .
```

Make sure to be on the root of the repository

For my case:
```
cd ~/git/rabbitmq
```

Open up the the MySQL shell:
```
sudo mysql -u root
```

Run the sql query file which configures several MySQL replication settings at the same time and enable database replication:
```
source sql_queries/configure_replica.sql
```

If the source command does not work, you have to stop the replica:
```
STOP REPLICA IO_THREAD FOR CHANNEL '';
```

**More Infomration on the SQL query file**
- After running this command, once you enable replication on this instance it will try to connect to the IP address  using the username and password.
  - `SOURCE_HOST` => IP address
  - `SOURCE_USER` => username
  - `SOURCE_PASSWORD` => password 
- It will also look for a binary log file and begin reading it from the position after the value of the position set. 
  - `SOURCE_LOG_FILE` => binary log file 
  - `SOURCE_LOG_POS` => position
- Be sure to replace `source_server_ip` with your source server’s IP address. 
- The `replica_user` and `password` should align with the replication user you created.
- The `mysql-bin.000001` log file and `4` sets the binary log coordinates.

If you did this correctly, the replica VM will begin replicating any changes made to the `ProjectDB` database on the source.

You can see details about the replica’s current state by running the following operation. The `\G` modifier in this command rearranges the text to make it more readable:

```
SHOW REPLICA STATUS\G;
```
  
Your replica is now replicating data from the source. Any changes you make to the source database will be reflected on the replica MySQL instance. You can test this by registering a new user and checking whether it gets replicated successfully.

### 7.1 Test if Database Replication Works
  
1. First register a new user on the website.  
  

2. Open up the MySQL shell on the source and replica VMs:

    <span style="color:red">Source</span> and <span style="color:green">Replica</span> VMs:

    ```
    sudo mysql -u root -p ProjectDB
    ```
  
3. Show the records of the users from the Source and Replica VMs:

    ```
    Select * from Users;
    ```

4. Make sure the User records match between the Source and Replica VMs.


**Note**: If either of these operations fail to do the database replication, it may be that you have an error somewhere in your replication configuration. In such cases, you could run the `SHOW REPLICA STATUS\G` operation to try finding the cause of the issue. 
Go to the Troubleshooting DB Replication section to solve this issue. 


## Troubleshooting DB Replication

### Troubleshootting Tips

Check the mysql error log file to figure out what error you are getting
```
vim /var/log/mysql/error.log
```

### Issue 1 — MySQL Relay Log Corrupted Fix

Stop the replication threads:
```
stop replica;
```

Make the replica forget its replication position in the source's binary log:
```
reset replica;
```

Set master log file and its log position:
```
CHANGE MASTER TO MASTER_LOG_FILE='mysql-bin.000001', MASTER_LOG_POS=4;
```

Start the replication threads:
```
start replica;
```

### Issue 2 — Replica Failed to Initilize Applier Metadata Structure from the repository Fix

Stop the replication threads:
```
stop replica;
```

Make the replica forget its replication position in the source's binary log:
```
reset replica;
```

Start the replication threads:
```
start replica;
```

### Issue 3 — Error 1062 – Duplicate Records Fix

Stop the replication threads:
```
stop replica;
```

Skip duplicate errors on a table:
```
set global sql_slave_skip_counter=1;
```

Start the replication threads:
```
start replica;
```

**Note**: Additionally, you can consult [MySQL’s documentation on troubleshooting](https://dev.mysql.com/doc/mysql-replication-excerpt/8.0/en/replication-problems.html) for suggestions on how to resolve replication problems.

## Resources
- [https://www.digitalocean.com/community/tutorials/how-to-set-up-replication-in-mysql](https://www.digitalocean.com/community/tutorials/how-to-set-up-replication-in-mysql)
- [MySQL relay log corrupted, how do I fix it? Tried but failed](https://dba.stackexchange.com/questions/53893/mysql-relay-log-corrupted-how-do-i-fix-it-tried-but-failed)
- [Solution to repairReplication: attempt to fix 'Slave failed to initialize relay log info structure from the repository (errno 1872)' #5067](https://github.com/vitessio/vitess/issues/5067#issuecomment-520168124)
- [Common MySQL Replication Issues](https://genexdbs.com/common-mysql-replication-issues/)
