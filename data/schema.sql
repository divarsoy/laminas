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