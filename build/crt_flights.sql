drop table flights CASCADE CONSTRAINTS;
drop sequence flights_seq; 

create table flights
	(id number(7) PRIMARY KEY,
	 flight_num number(7),
	 dept_city varchar(3) REFERENCES airports (code),
	 arr_city varchar(3) REFERENCES airports (code),
	 flight_date date,
	 dept_time number(4),
	 arr_time number(4),
	 distance number(4),
	 tail_num varchar(7) REFERENCES planes (tail_num),
	 pilots_needed number(2),
	 fa_needed number(2)
	);

create sequence flights_seq START WITH 1;
