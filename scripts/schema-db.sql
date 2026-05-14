DROP TABLE IF EXISTS tamalbit CASCADE;
DROP TABLE IF EXISTS compra CASCADE;
DROP TABLE IF EXISTS producto CASCADE;
DROP TABLE IF EXISTS categoria CASCADE;
DROP TABLE IF EXISTS usuario CASCADE;

CREATE TABLE usuario (
    id_usuario SERIAL PRIMARY KEY,
    person_id VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    ultimo_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categoria (
    id_categoria SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL
);

CREATE TABLE producto (
    id_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen_url VARCHAR(500),
    id_categoria INT NOT NULL,
    es_tamalbit BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

CREATE TABLE compra (
    id_compra SERIAL PRIMARY KEY,
    person_id VARCHAR(50) NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    monto DECIMAL(10, 2) NOT NULL,
    descripcion VARCHAR(255),
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    saldo_antes DECIMAL(10, 2) NOT NULL,
    saldo_despues DECIMAL(10, 2) NOT NULL,
    tamalbits_obtenidos INT DEFAULT 0,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE tamalbit (
    id_tamalbit SERIAL PRIMARY KEY,
    person_id VARCHAR(50) NOT NULL,
    cantidad INT NOT NULL,
    origen_compra_id INT REFERENCES compra(id_compra),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categoria (nombre, descripcion) VALUES
('Cerdo', 'Cortes finos de cerdo'),
('Res', 'Cortes premium de res'),
('Pollo', 'Especialidades de pollo'),
('Embutidos', 'Embutidos artesanales');

INSERT INTO producto (nombre, descripcion, precio, imagen_url, id_categoria, es_tamalbit) VALUES
('Orejas de Pollo', 'Crujientes orejas de pollo empanizadas', 10.00, 'https://images.pexels.com/photos/60616/fried-chicken-chicken-fried-crunchy-60616.jpeg?auto=compress&cs=tinysrgb&w=400', 3, TRUE),
('Punta de Anca', 'Corte premium de res sellado a la perfeccion', 24.90, 'https://images.pexels.com/photos/65175/pexels-photo-65175.jpeg?auto=compress&cs=tinysrgb&w=400', 2, FALSE),
('Chuzo de Pollo', 'Brocheta de pollo marinado con especias', 15.00, 'https://images.pexels.com/photos/3928854/pexels-photo-3928854.png?auto=compress&cs=tinysrgb&w=400', 3, FALSE),
('Prosciutto', 'Jamon curado artesanal italiano', 18.20, 'https://images.pexels.com/photos/1930760/pexels-photo-1930760.jpeg?auto=compress&cs=tinysrgb&w=400', 4, FALSE),
('Filete de Wagyu', 'Corte de res importado marmoleado', 85.00, 'https://images.pexels.com/photos/361184/asparagus-steak-veal-steak-veal-361184.jpeg?auto=compress&cs=tinysrgb&w=400', 2, FALSE),
('Cochinita', 'Cerdo estilo cochinita pibil', 14.50, 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=400&q=80', 1, FALSE);
