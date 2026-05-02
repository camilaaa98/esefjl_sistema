# 🏥 Sistema de Gestión Farmacéutica ESE Fabio Jaramillo (ESEFJL)

Sistema institucional de alto rendimiento diseñado para la gestión de inventarios, monitoreo de vencimientos y vinculación de pacientes en la red hospitalaria.

## 🚀 Despliegue en Render / Linux

Este sistema está optimizado para ejecutarse en entornos Linux con PostgreSQL (Supabase).

### Requisitos Técnicos
- PHP 8.1+
- Extensión pdo_pgsql habilitada.
- Base de datos PostgreSQL (Producción) o SQLite (Desarrollo).

### Configuración de Variables de Entorno
Crea un archivo .env en la raíz (o configura las variables en el panel de Render):

`env
DATABASE_URL=postgresql://user:pass@host:5432/dbname
APP_ENV=production
`

### Estructura de Directorios
- pp/: Lógica de negocio (Controladores, Repositorios, Configuración).
- public/: Punto de entrada y recursos públicos (CSS, JS).
- esources/views/: Interfaces de usuario.
- database/: Esquemas SQL y base de datos local.
- img/: Activos visuales institucionales.

## 🛡️ Seguridad
Los scripts de diagnóstico y mantenimiento han sido movidos a /scripts/ y están protegidos por .htaccess.

## 📄 Licencia
Propiedad institucional - ESE Fabio Jaramillo.
