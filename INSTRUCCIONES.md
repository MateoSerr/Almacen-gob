# 🚀 INSTRUCCIONES PARA INICIAR EL SISTEMA DE INVENTARIO

## Opción 1: Usar SQLite (RECOMENDADO - No necesitas XAMPP)

### Paso 1: Abrir terminal en la carpeta del proyecto
Abre PowerShell o terminal en la carpeta del proyecto

### Paso 2: Crear archivo .env (si no existe)
```powershell
php -r "file_exists('.env') || copy('.env.example', '.env');"
```

O si no existe .env.example, crear manualmente el archivo .env con este contenido:
```
APP_NAME="Sistema de Inventario"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Paso 3: Generar la clave de la aplicación
```powershell
php artisan key:generate
```

### Paso 4: Ejecutar las migraciones (crear las tablas)
```powershell
php artisan migrate
```

### Paso 5: Cargar los productos del inventario
```powershell
php artisan db:seed --class=ProductoSeeder
```

O ejecutar todo junto (migraciones + seeders):
```powershell
php artisan migrate --seed
```

### Paso 6: Iniciar el servidor de Laravel
```powershell
php artisan serve
```

### Paso 7: Abrir en el navegador
Abre tu navegador y ve a: **http://localhost:8000**

---

## Opción 2: Usar MySQL con XAMPP (Si prefieres MySQL)

### Paso 1: Iniciar XAMPP
- Abre XAMPP Control Panel
- Inicia **Apache** y **MySQL** (botones "Start")

### Paso 2: Crear base de datos
1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Crea una nueva base de datos llamada: `inventario`

### Paso 3: Configurar .env
Edita el archivo `.env` y cambia:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario
DB_USERNAME=root
DB_PASSWORD=
```

### Paso 4: Ejecutar migraciones y seeders
```powershell
php artisan migrate --seed
```

### Paso 5: Iniciar servidor
```powershell
php artisan serve
```

### Paso 6: Abrir en navegador
Abre: **http://localhost:8000**

---

## 📋 Resumen de comandos (SQLite - Recomendado)

```powershell
# 1. Crear .env si no existe
php -r "file_exists('.env') || copy('.env.example', '.env');"

# 2. Generar clave
php artisan key:generate

# 3. Crear tablas y cargar datos
php artisan migrate --seed

# 4. Iniciar servidor
php artisan serve
```

Luego abre: **http://localhost:8000**

---

## ✅ ¡Listo!

Una vez iniciado, podrás:
- Ver productos con alertas (stock < 100)
- Registrar entradas y salidas
- Ver reportes (mínimos, máximos, promedios)
- Gestionar todo el inventario


