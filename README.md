# Sistema de Gestión Farmacéutica ESEFJL v7.5 Premium

## Arquitectura de Software
Este sistema ha sido diseñado bajo estándares de ingeniería de software de alto nivel, aplicando principios **SOLID** y una arquitectura de capas desacoplada.

### Estructura de Directorios (Organización Profesional)
- **`/assets`**: Recursos estáticos (CSS con variables, JS modular, Animaciones).
- **`/core`**: Núcleo lógico del sistema.
  - **`/Controllers`**: Orquestadores de la lógica de negocio. Implementan Inyección de Dependencias (DIP).
  - **`/Infrastructure`**: Capa de persistencia y utilidades base (Database, Auth, ViewHelpers).
  - **`/Repositories`**: Capa de acceso a datos pura. Separa las consultas SQL de la lógica del controlador (SRP).
- **`/docs`**: Documentación técnica y científica profesional (Normas APA 7).
- **`/views`**: Interfaces de usuario responsivas y optimizadas.
- **`/includes`**: Componentes reutilizables de UI (Sidebar, Headers).

### Principios Implementados
1.  **S (Single Responsibility)**: Cada repositorio se encarga exclusivamente de una entidad de la base de datos.
2.  **O (Open/Closed)**: Sistema de repositorios extensible mediante `BaseRepository`.
3.  **D (Dependency Inversion)**: Los controladores dependen de abstracciones de datos, facilitando pruebas y escalabilidad.
4.  **Responsividad**: Diseño adaptable mediante CSS Grid y Flexbox para operación en tablets y dispositivos móviles.

---
*Desarrollado para la Excelencia Institucional — ESE Fabio Jaramillo Londoño*
