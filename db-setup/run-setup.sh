userpass=$1
mysql -u root -p -e"set @userpass='${userpass}'; `cat db-setup.sql`"
