-- Base de datos
CREATE DATABASE IF NOT EXISTS `index` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `index`;

-- --------------------------------------------------------
-- TABLA: libros
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS libros (
    id VARCHAR(20) PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255),
    autores TEXT,
    editorial VARCHAR(100),
    fecha_publicacion VARCHAR(20),
    descripcion TEXT,
    isbn_10 VARCHAR(10),
    isbn_13 VARCHAR(13),
    paginas INT,
    categoria VARCHAR(100),
    imagen_url VARCHAR(255),
    idioma VARCHAR(10),
    preview_link VARCHAR(255),
    fecha_importacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLA: peliculas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    anio INT,
    portada VARCHAR(255),
    descripcion TEXT,
    genero VARCHAR(100)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLA: usuarios
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    contrasena VARCHAR(200) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    pais VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLA: comentarios
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    id_libro VARCHAR(20) NULL,
    id_pelicula INT NULL,
    texto TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
>>>>>>> a9e0df1ad91779ec3f70ac784717de2b9ba0aec0

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    FOREIGN KEY (id_libro) REFERENCES libros(id)
        ON DELETE SET NULL ON UPDATE CASCADE,

    FOREIGN KEY (id_pelicula) REFERENCES peliculas(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- TABLA: listas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS listas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
