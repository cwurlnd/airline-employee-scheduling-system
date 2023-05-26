load data infile 'flightattendants.csv'
insert into table flightattendants
fields terminated by "," optionally enclosed by "'"
(user_id)
