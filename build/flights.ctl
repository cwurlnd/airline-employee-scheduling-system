load data infile 'flights.csv'
insert into table flights
fields terminated by "," optionally enclosed by "'"
(flight_num,dept_city,arr_city,flight_date DATE "MM/DD/YYYY",dept_time,arr_time,distance,tail_num, pilots_needed, fa_needed)
