# Sistema de Inventario de Almacén

Sistema web desarrollado en Laravel para la gestión de inventario de almacén, con funcionalidades para registrar entradas, salidas, productos, y generar reportes.

## Características

- ✅ Gestión de productos con categorías
- ✅ Registro de entradas y salidas de almacén
- ✅ Control de stock con alertas de inventario mínimo
- ✅ Gestión de oficios de entrada
- ✅ Registro de salidas para policías (uniformes y equipo)
- ✅ Reportes de inventario (mínimos, máximos, promedios)
- ✅ Generación de documentos Word para oficios
- ✅ Búsqueda avanzada de productos

## Requisitos

- PHP >= 8.2
- Composer
- Node.js y NPM
- SQLite (recomendado) o MySQL

## Instalación

1. Clonar el repositorio:
```bash
git clone <url-del-repositorio>
cd mi_proyecto
```

2. Instalar dependencias:
```bash
composer install
npm install
```

3. Configurar el archivo `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

O para MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario
DB_USERNAME=root
DB_PASSWORD=
```

5. Ejecutar migraciones y seeders:
```bash
php artisan migrate --seed
```

6. Compilar assets:
```bash
npm run build
```

7. Iniciar el servidor:
```bash
php artisan serve
```

El sistema estará disponible en `http://localhost:8000`

## Estructura del Proyecto

```
mi_proyecto/
├── app/
│   ├── Http/Controllers/    # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── Helpers/              # Funciones auxiliares
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/             # Seeders de datos
├── resources/
│   └── views/               # Vistas Blade
└── routes/
    └── web.php              # Rutas web
```

## Tecnologías Utilizadas

- **Backend**: Laravel 12
- **Frontend**: Blade Templates, TailwindCSS
- **Base de Datos**: SQLite / MySQL
- **Librerías**: PHPWord, PHPSpreadsheet

## Licencia

Este proyecto es de uso interno.
