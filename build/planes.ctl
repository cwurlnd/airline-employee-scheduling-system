load data infile 'planes.csv'
insert into table planes
fields terminated by "," optionally enclosed by "'"
(tail_num,seats,manuf)
