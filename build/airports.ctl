load data infile 'airports.csv'
insert into table airports
fields terminated by "," optionally enclosed by "'"
(code,airport_name,city,airport_state)
