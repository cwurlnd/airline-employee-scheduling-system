drop table flys CASCADE CONSTRAINTS;

create table flys
	(pilot_id number(5) REFERENCES pilots(user_id),
	 flight_id number(7) REFERENCES flights(id),
	 curr_status varchar(1)
	);	
