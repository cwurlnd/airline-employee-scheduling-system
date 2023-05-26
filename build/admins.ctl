load data infile 'admins.csv'
insert into table admins
fields terminated by "," optionally enclosed by "'"
(user_id)
