OdHCL - Migración a PHP / XAMPP

Este repositorio contiene la versión del frontend (HTML/CSS/JS) adaptada para usar un backend PHP simple y una base de datos SQLite por defecto. Está pensada para correr en XAMPP (Windows) pero puede adaptarse fácilmente a MySQL.

Archivos añadidos:
- `index.php` - Punto de entrada: versión PHP del HTML
- `api/` - Endpoints PHP: `db.php`, `pacientes.php`, `citas.php`, `boletas.php`, `precios.php`, `config.php`
- `sql/db.sql` - Esquema inicial (SQLite)
- `assets/app.js` - Actualizado para consumir la API mediante fetch
- `data/` - carpeta donde se creará `odhcl.sqlite` (si usas SQLite)

Cómo ejecutar con XAMPP (SQLite, opción por defecto):
1. Copia la carpeta del proyecto a `C:\xampp\htdocs\tu-carpeta`.
2. Asegúrate de que el servidor Apache esté en ejecución desde el panel de XAMPP.
3. Abre en el navegador: http://localhost/tu-carpeta/index.php

Inicializar esquema SQLite (si no se crea automáticamente):
- El script PHP crea automáticamente el archivo `data/odhcl.sqlite` y usará PDO. Si prefieres crear manualmente la DB con sqlite3, ejecuta:

```powershell
# desde PowerShell, ubicado en la carpeta del proyecto
# crear carpeta data si no existe
mkdir data; sqlite3 data\odhcl.sqlite < sql\db.sql
```


Usar MySQL / MariaDB (XAMPP)

Por conveniencia ya dejé `api/db.php` intentando conectar a MySQL (host 127.0.0.1, bd `odhcl`, usuario `root`, sin contraseña) y, si falla la conexión, hace fallback a SQLite.

Pasos rápidos para inicializar MySQL:

1. Inicia Apache y MySQL desde el panel XAMPP.
2. Abre en el navegador (ajusta la ruta según tu carpeta):
	http://localhost/tu-carpeta/api/init_mysql.php
	Esto crea la base `odhcl` y ejecuta el SQL definido en `sql/db_mysql.sql`.

Si prefieres hacerlo con phpMyAdmin, puedes importar `sql/db_mysql.sql` manualmente.

Si tu MySQL tiene credenciales distintas, edita `api/db.php` y actualiza la sección `$mysql` con tu host/usuario/password/nombre de BD.

Notas y limitaciones:
- La API es mínima y no implementa autenticación.
- Las operaciones en lote (p. ej. dedupePacientes) intentan persistir cada paciente individualmente.
- En desarrollo puedes inspeccionar las peticiones XHR en el panel de red del navegador.

Siguientes pasos recomendados:
- Añadir autenticación (sesiones) y control de acceso.
- Agregar validación y manejo de errores más robusto en el backend.
- Implementar endpoints para import/export masivo si necesitas migración de datos.

Si quieres, puedo adaptar la API para MySQL ahora y ejecutar el script SQL automáticamente o añadir endpoints adicionales (buscador por DNI, paginación, etc.).
