load data infile 'users.csv'
insert into table users
fields terminated by "," optionally enclosed by "'"
(email,password,first_name,last_name)
