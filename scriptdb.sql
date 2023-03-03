create database dos_tablas;

use dos_tablas;

create table biblioteca (
    id_biblioteca int primary key auto_increment,
    nombre_biblioteca varchar(50) not null,
    direccion_biblioteca varchar(50) not null,
    provincia_biblioteca varchar(50) not null,
    ciudad_biblioteca varchar(50) not null,
    telefono_biblioteca varchar(15) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

create table libros(
  id_libros int primary key auto_increment,
  nombre_libro varchar(50) not null,
  isbn_libro varchar(15) not null,
  autor varchar(15) not null,
  email varchar(30) not null,
  biblioteca_id INT,
  FOREIGN KEY (biblioteca_id) REFERENCES biblioteca(id_biblioteca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


