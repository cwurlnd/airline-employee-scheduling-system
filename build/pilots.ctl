load data infile 'pilots.csv'
insert into table pilots
fields terminated by "," optionally enclosed by "'"
(user_id)
