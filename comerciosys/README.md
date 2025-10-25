# 🧾 ComercioSys - Sistema de Gestión de Ventas

Sistema web completo de gestión de ventas y usuarios, desarrollado con PHP, MySQL y tecnologías web modernas.

## 🚀 Características

- **Sistema de Autenticación**: Login seguro con diferentes roles de usuario
- **Gestión de Ventas**: Registro, edición y eliminación de ventas con cálculos automáticos
- **Gestión de Usuarios**: CRUD completo para administradores
- **Dashboard Interactivo**: Panel principal con estadísticas en tiempo real
- **Interfaz Moderna**: Diseño responsive y profesional
- **Reportes**: Generación de reportes de ventas
- **Seguridad**: Prevención de SQL Injection y control de acceso por roles

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8+ (sin frameworks)
- **Base de Datos**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Servidor**: Apache (XAMPP)
- **Arquitectura**: MVC básica

## 📋 Requisitos del Sistema

- XAMPP (Apache + MySQL + PHP)
- PHP 8.0 o superior
- MySQL 5.7 o superior
- Navegador web moderno

## 🔧 Instalación

### 1. Configurar XAMPP

1. Descargar e instalar XAMPP desde [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Iniciar Apache y MySQL desde el panel de control de XAMPP

### 2. Configurar la Base de Datos

1. Abrir phpMyAdmin en [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Crear la base de datos `comerciosys_db`
3. Ejecutar el siguiente script SQL:

```sql
CREATE DATABASE IF NOT EXISTS comerciosys_db;
USE comerciosys_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_cod INT PRIMARY KEY,
    apellido VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    nick VARCHAR(50) UNIQUE NOT NULL,
    pass VARCHAR(50) NOT NULL,
    rol ENUM('Administrador', 'Soporte', 'Caja') NOT NULL
);

-- Tabla de cobranza (ventas)
CREATE TABLE cobranza (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cod_usuario INT,
    concepto VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_cod_usuario) REFERENCES usuarios(id_cod)
);

-- Datos iniciales
INSERT INTO usuarios (id_cod, apellido, nombre, nick, pass, rol) VALUES 
(101, 'Cuentas', 'Omar', 'user1', '12345', 'Soporte'), 
(102, 'Gallegos', 'Nestor', 'user2', '1234', 'Caja'), 
(103, 'Neira', 'Ruben', 'Admin', '123456', 'Administrador');

INSERT INTO cobranza (id_cod_usuario, concepto, fecha, hora, cantidad, precio_unitario, total) VALUES
(102, 'Televisor 38°', '2025-10-02', '13:15:00', 2, 650.00, 1300.00),
(102, 'Equipo de sonido', '2025-10-02', '13:16:00', 1, 1500.00, 1500.00),
(102, 'Microondas', '2025-10-02', '14:45:00', 2, 450.00, 900.00);
```

### 3. Instalar el Sistema

1. Copiar la carpeta `comerciosys` a `C:\xampp\htdocs\`
2. La estructura final debe ser: `C:\xampp\htdocs\comerciosys\`

### 4. Acceder al Sistema

1. Abrir el navegador
2. Ir a [http://localhost/comerciosys](http://localhost/comerciosys)
3. El sistema redirigirá automáticamente al login

## 👥 Usuarios Disponibles

| Rol | Usuario | Contraseña |
|-----|---------|------------|
| Administrador | Admin | 123456 |
| Soporte | user1 | 12345 |
| Caja | user2 | 1234 |

## 📁 Estructura del Proyecto

```
comerciosys/
├── database/
│   └── connection.php          # Conexión a la base de datos
├── includes/
│   ├── auth.php               # Sistema de autenticación
│   └── functions.php           # Funciones auxiliares
├── css/
│   └── style.css              # Estilos del sistema
├── js/
│   └── script.js              # JavaScript del sistema
├── login.php                  # Página de login
├── dashboard.php              # Panel principal
├── ventas.php                 # Módulo de ventas
├── usuarios.php               # Módulo de usuarios
├── logout.php                 # Cerrar sesión
├── index.php                  # Página principal
└── README.md                  # Este archivo
```

## 🎯 Funcionalidades

### 🔐 Sistema de Autenticación
- Login con usuario y contraseña
- Control de sesiones
- Redirección automática según el estado de login
- Diferentes niveles de acceso por rol

### 📊 Dashboard
- Bienvenida personalizada
- Fecha y hora actual
- Estadísticas de ventas y usuarios
- Accesos directos a módulos

### 💰 Gestión de Ventas
- Registro de nuevas ventas
- Edición y eliminación de ventas
- Cálculo automático del total
- Búsqueda por fecha y concepto
- Lista completa de ventas

### 👥 Gestión de Usuarios (Solo Administrador)
- Crear nuevos usuarios
- Editar usuarios existentes
- Eliminar usuarios
- Lista completa de usuarios
- Validación de datos

### 🧾 Reportes
- Generación de reportes de ventas
- Filtros por fecha y usuario
- Opciones de impresión

## 🎨 Diseño

- **Colores**: Azul, blanco y gris como base
- **Tipografía**: Segoe UI para mejor legibilidad
- **Responsive**: Adaptable a diferentes tamaños de pantalla
- **Iconos**: Font Awesome para una interfaz moderna
- **Animaciones**: Transiciones suaves y efectos hover

## 🔒 Seguridad

- **Prepared Statements**: Prevención de SQL Injection
- **Validación de Datos**: Validación tanto en frontend como backend
- **Control de Acceso**: Restricciones por rol de usuario
- **Sesiones Seguras**: Manejo seguro de sesiones de usuario

## 🚀 Uso del Sistema

### 1. Iniciar Sesión
- Acceder a [http://localhost/comerciosys](http://localhost/comerciosys)
- Usar las credenciales proporcionadas
- El sistema redirigirá automáticamente al dashboard

### 2. Dashboard
- Ver estadísticas generales
- Acceder a los diferentes módulos
- Navegar por el sistema

### 3. Gestión de Ventas
- Registrar nuevas ventas con todos los campos requeridos
- El total se calcula automáticamente
- Buscar ventas por fecha o concepto
- Editar o eliminar ventas existentes

### 4. Gestión de Usuarios (Solo Administrador)
- Crear usuarios con ID único
- Asignar roles apropiados
- Editar información de usuarios
- Eliminar usuarios (excepto el propio)

## 🐛 Solución de Problemas

### Error de Conexión a la Base de Datos
- Verificar que MySQL esté ejecutándose en XAMPP
- Comprobar que la base de datos `comerciosys_db` existe
- Verificar las credenciales en `database/connection.php`

### Error 404 - Página No Encontrada
- Verificar que Apache esté ejecutándose
- Comprobar que los archivos estén en `C:\xampp\htdocs\comerciosys\`
- Verificar la URL: `http://localhost/comerciosys`

### Problemas de Permisos
- Verificar que el usuario tenga los permisos correctos
- Solo los administradores pueden gestionar usuarios
- Los usuarios de caja pueden gestionar ventas

## 📞 Soporte

Para soporte técnico o reportar problemas:
- Verificar que todos los requisitos estén cumplidos
- Revisar los logs de error de Apache y PHP
- Comprobar la configuración de la base de datos

## 📄 Licencia

Este proyecto es de uso educativo y demostrativo.

---

**ComercioSys** - Sistema de Gestión de Ventas  
Desarrollado con ❤️ para gestión empresarial
