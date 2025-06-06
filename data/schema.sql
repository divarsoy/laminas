CREATE TABLE album (id INTEGER PRIMARY KEY AUTOINCREMENT, artist varchar(100) NOT NULL, title varchar(100) NOT NULL);
INSERT INTO album (artist, title) VALUES ('The Military Wives', 'In My Dreams');
INSERT INTO album (artist, title) VALUES ('Adele', '21');
INSERT INTO album (artist, title) VALUES ('Bruce Springsteen', 'Wrecking Ball (Deluxe)');
INSERT INTO album (artist, title) VALUES ('Lana Del Rey', 'Born to Die');
INSERT INTO album (artist, title) VALUES ('Gotye', 'Making Mirrors');


CREATE TABLE apartment (id INTEGER PRIMARY KEY AUTOINCREMENT, name varchar(255) NOT NULL, city varchar(100) NOT NULL);
INSERT INTO apartment (name, city) VALUES ('The Gate London City', 'London');
INSERT INTO apartment (name, city) VALUES ('The Chronicle Aparthotel', 'London');
INSERT INTO apartment (name, city) VALUES ('Ember Locke', 'London');
INSERT INTO apartment (name, city) VALUES ('Bermonds Locke', 'London');
INSERT INTO apartment (name, city) VALUES ('Stay Camden Serviced Apartments', 'London');

CREATE TABLE property (id INTEGER PRIMARY KEY AUTOINCREMENT, name varchar(255) NOT NULL, location_id INTEGER NOT NULL, emission varchar(50), rate INTEGER, imageurl VARCHAR(255), FOREIGN KEY(location_id) REFERENCES location(id));
CREATE TABLE apartment_type (id INTEGER PRIMARY KEY AUTOINCREMENT, type varchar(100));
CREATE TABLE property_apartment_type (id INTEGER PRIMARY KEY AUTOINCREMENT, property_id INTEGER NOT NULL, apartment_type_id INTEGER NOT NULL,  FOREIGN KEY(property_id) REFERENCES property(id), FOREIGN KEY(apartment_type_id) REFERENCES apartment_type(id));
CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT, area varchar(255) NOT NULL, city varchar(255) NOT NULL);

INSERT INTO location(id, area, city) VALUES (1, 'Aldgate', 'London');
INSERT INTO location(id, area, city) VALUES (2, 'Chancery Lane', 'London');
INSERT INTO location(id, area, city) VALUES (3, 'Kensington', 'London');
INSERT INTO location(id, area, city) VALUES (4, 'London Bridge', 'London');
INSERT INTO location(id, area, city) VALUES (5, 'Camden', 'London');
INSERT INTO location(id, area, city) VALUES (6, 'Clerkenwell', 'London');
INSERT INTO location(id, area, city) VALUES (7, 'Canary Wharf', 'London');
INSERT INTO location(id, area, city) VALUES (8, 'Earls Court', 'London');
INSERT INTO location(id, area, city) VALUES (9, 'Westminister', 'London');
INSERT INTO location(id, area, city) VALUES (10, 'Paddington', 'London');
INSERT INTO location(id, area, city) VALUES (11, 'Wembley', 'London');
INSERT INTO location(id, area, city) VALUES (12, 'Southwark', 'London');
INSERT INTO location(id, area, city) VALUES (13, 'Marylebone', 'London');

INSERT INTO apartment_type(id, type) VALUES (1, '1 Bed');
INSERT INTO apartment_type(id, type) VALUES (2, '2 Bed');
INSERT INTO apartment_type(id, type) VALUES (3, '3 Bed');
INSERT INTO apartment_type(id, type) VALUES (4, '4 Bed');
INSERT INTO apartment_type(id, type) VALUES (5, 'Studio');
INSERT INTO apartment_type(id, type) VALUES (6, 'Other');

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (1, 'The Gate London City', 1, '6.21kg CO2e', 129, 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (1,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (1,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (1,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (2, 'The Chronicle Aparthotel', 2, '8.54kg CO2e', null, 'https://images.unsplash.com/photo-1515263487990-61b07816b324?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (2,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (2,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (2,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (2,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (3, 'Ember Locke', 3, '5.1kg CO2e', 165, 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwzfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (3,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (3,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (4, 'Bermonds Locke', 4, '11.97kg CO2e', 165, 'https://images.unsplash.com/photo-1460317442991-0ec209397118?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw0fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (4,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (4,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (4,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (5, 'STAY Camden Serviced Apartments', 5, '<1kg CO2e', null, 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw1fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (5,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (5,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (5,3);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (5,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (6, 'The Rosebery Aparthotel', 6, '7.54kg CO2e', null, 'https://images.unsplash.com/photo-1523192193543-6e7296d960e4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw2fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (6,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (6,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (6,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (7, 'Cove Landmark Pinnacle', 7, '5.98kg CO2e', 203, 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw3fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (7,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (7,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (7,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (8, 'Templeton Place Aparthotel', 8, '8.24kg CO2e', null, 'https://images.unsplash.com/photo-1502672023488-70e25813eb80?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw4fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (8,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (8,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (8,6);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (9, 'Buckingham Palace Residences by Aeria Apartments', 9, '13.91kg CO2e', 266, 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHw5fHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (9,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (9,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (9,3);

INSERT INTO property(id, name, location_id, emission, rate, imageurl) VALUES (10, 'Nevern Place Aparthotel', 8, '7.06kg CO2e', null, 'https://images.unsplash.com/photo-1459767129954-1b1c1f9b9ace?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxMHx8YXBhcnRtZW50fGVufDB8fHx8MTc0ODg4NjE1N3ww&ixlib=rb-4.1.0&q=80&w=800');
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (10,5);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (10,1);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (10,2);
INSERT INTO property_apartment_type('property_id', 'apartment_type_id') VALUES (10,6);
