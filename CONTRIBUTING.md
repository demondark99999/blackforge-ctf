# Guía de Contribución - BlackForge Labs Instalación Omega

¡Gracias por su interés en contribuir a BlackForge Labs Instalación Omega! 
Agradecemos las contribuciones de la comunidad para mejorar este entorno de entrenamiento de ciberseguridad.

## Cómo Contribuir

Hay muchas formas de contribuir a este proyecto:

### Reportar Problemas
Si encuentra un error, tiene una sugerencia para mejorar o quiere reportar un problema de seguridad:
1. Verifique si el problema ya ha sido reportado en los [Issues](https://github.com/blackforgelabs/instalacion-omega/issues)
2. Si no existe, cree un nuevo issue siguiendo la plantilla proporcionada
3. Incluya tanta información como sea posible:
   - Descripción clara y concisa del problema
   - Pasos para reproducirlo (si aplica)
   - Comportamiento esperado vs. comportamiento real
   - Capturas de pantalla o logs relevantes (asegúrese de no incluir información sensible)
   - Versión del entorno y componentes afectados

### Sugerir Mejoras
Agradecemos sugerencias para nuevos features, mejoras de documentación o ideas para escenarios de entrenamiento:
1. Use la plantilla de "Feature Request" al crear un nuevo issue
2. Proporcione detalles sobre:
   - El problema o oportunidad que aborda la mejora
   - Cómo beneficiaría a los usuarios del entorno
   - Cualquier consideración técnica o de implementación
   - Recursos o referencias que respalden la sugerencia

### Contribuir con Código
Si desea contribuir con código, siga estos pasos:

#### 1. Preparar su entorno de desarrollo
- Haga un fork del repositorio
- Clone su fork localmente: `git clone https://github.com/TU_USUARIO/instalacion-omega.git`
- Cree una rama para su contribución: `git checkout -b feature/name-of-your-feature`
- Asegúrese de tener Docker y Docker Compose instalados y configurados

#### 2. Realizar sus cambios
- Siga las convenciones de código establecidas en el proyecto
- Escriba código claro, bien comentado y testeado
- Asegúrese de que sus cambios no introduzcan vulnerabilidades de seguridad no intencionales
- Para cambios que affecten funcionalidad existente, considere agregar o actualizar pruebas
- Documente adecuadamente cualquier nueva funcionalidad o cambio significativo

#### 3. Mantener la coherencia con el proyecto
- Los commits deben ser atómicos y tener mensajes descriptivos siguiendo convencional commits
- Mantenga sus cambios enfocados en un solo tema o funcionalidad por pull request
- Si su contribución es grande, considere dividirla en múltiples pull requests más pequeños
- Asegúrese de que sus cambios sean compatibles con la filosofía del proyecto de entrenamiento realista

#### 4. Enviar su contribución
- Push de sus cambios a su fork: `git push origin feature/name-of-your-feature`
- Cree un nuevo Pull Request desde su fork al repositorio principal
- Complete la plantilla de pull request con toda la información requerida
- Espere la revisión y retroalimentación del equipo de mantenimiento
- Esté preparado para hacer cambios basado en la retroalimentación recibida

## Estándares de Código

### General
- Siga PSR-12 para código PHP
- Use comentarios claros y concisos en inglés o español
- Evite código duplicado - extraiga lógica común a funciones o clases reutilizables
- Mantenga las funciones enfocadas en una sola responsabilidad (Single Responsibility Principle)
- Use nombres descriptivos para variables, funciones y clases
- Evite código "mágico" - si es necesario, documente exhaustivamente su propósito y funcionamiento

### PHP Selecifico
- Use declaración de tipos estrictos (`declare(strict_types=1);`) cuando sea apropiado
- Aproveche las características modernas de PHP (8.0+) cuando beneficien la claridad y seguridad
- Use PDO para interacciones con base de datos con consultas preparadas
- Implemente manejo adecuado de excepciones
- Evite el uso de variables globales cuando sea posible
- Use inyección de dependencias para gestionar dependencias entre componentes

### JavaScript/ESpecífico
- Use características modernas de ES6+ quando beneficiaren la legibilidad
- Mantenga las funciones petites y enfocadas
- Use const y let en lugar de var cuando sea apropiado
- Implemente manejo adecuado de errores en operaciones asíncronas
- Evite manipulación directa del DOM cuando se puedan usar frameworks o bibliotecas apropiadas
- Use módulos ES cuando la complejidad lo justifique

### HTML/CSS
- Use HTML5 semántico cuando sea posible
- Siga metodologías BEM o similiares para organización de CSS
- Use CSS moderno (Flexbox, Grid) para diseños complejos
- Asegure accesibilidad siguiendo WCAG 2.1 cuando sea apropiado
- Optimice para rendimiento: minimice reflows y repaints
- Use comentarios para explicar decisiones de diseño no obvias

### Documentación
- Escriba toda la documentación en markdown (.md)
- Use un estilo claro, conciso y profesional
- Incluya ejemplos de código cuando sea apropiado
- Mantenga la documentación actualizada con los cambios en el código
- Siga el mismo tono y estilo que el resto de la documentación del proyecto
- Traduzca a otros idiomas solo cuando se tenga capacidad para mantener las traducciones actualizadas

### Commits y Mensajes
Use el formato de [Commit Messages Convencionales](https://www.conventionalcommits.org/):
```
<tipo>[alcance opcional]: <descripción>

[cuerpo opcional]

[pie opcional]
```

Donde `<tipo>` es uno de:
- `feat`: Nueva característica
- `fix`: Corrección de error
- `docs`: Cambios en documentación
- `style`: Cambios que no afectan el significado del código (espacios, formato, etc.)
- `refactor`: Cambios de estructura que no corrigen errores ni añaden características
- `perf`: Cambios de mejora de rendimiento
- `test`: Añadir o corregir pruebas
- `chore`: Cambios en el proceso de construcción o herramientas auxiliares

Ejemplos de buenos mensajes:
- `feat(auth): agregar soporte para autenticación multifactor`
- `fix(api): corregir manejo de errores en endpoint de consulta`
- `docs(readme): actualizar instrucciones de despliegue para nuevas versiones de Docker`
- `refactor(services): extraer lógica común de manejo de archivos a servicio separado`
- `test(web): agregar pruebas unitarias para validación de entrada en formulario de login`

## Proceso de Revisión de Pull Requests

1. **Revisión Inicial**: Un miembro del equipo verificará que el PR cumple con los requisitos básicos
2. **Revisión Técnica**: Evaluación de la calidad, seguridad y adecuación técnica del código
3. **Revisión de Seguridad**: Revisión específica para asegurarse de que no se introduzcan vulnerabilidades no intencionales
4. **Revisión de Documentación**: Verificación de que la documentación acompañe adecuadamente los cambios
5. **Pruebas**: Ejecución de pruebas automatizadas y revisiones manuales cuando sea necesario
6. **Aprobación**: Se requiere al menos una aprobación de un miembro del equipo antes de fusionar
7. **Fusión**: Después de la aprobación, el PR puede fusionarse usando el método apropiado (merge, squash, rebase)

## Reporte de Problemas de Seguridad

Si descubre una vulnerabilidad de seguridad en este entorno que podría ser explotada fuera del contexto de entrenamiento:
1. **NO** la revele públicamente en issues o discusiones
2. Envíe un correo electrónico directamente a: security@forgelabs.local
3. Incluya en su reporte:
   - Descripción detallada de la vulnerabilidad
   - Pasos para reproducirla
   - Impacto potencial si se explotara fuera del entorno de entrenamiento
   - Cualquier mitigación o solución que haya identificado
   - Información de contacto para seguimiento
4. Esperar una respuesta dentro de los 7-10 días hábiles
5. Siga las indicaciones del equipo de seguridad para coordinar la divulgación responsable

## Reconocimientos

Todos los contribuidores que fusionen código exitosamente serán reconocidos en:
- El archivo `CONTRIBUTORS.md` (generado automáticamente)
- La sección de agradecimientos en la documentación relevante
- Notas de lanzamiento en releases significativos
- Otras formas de reconocimiento según lo determine el equipo de mantenimiento

## Preguntas Frecuentes

### P: ¿Puedo contribuir con escenarios de entrenamiento o ejercicios?
**R**: ¡Absolutamente! Los escenarios de entrenamiento son una forma muy valiosa de contribuir. Por favor siga las guías de documentación para asegurar que sus escenarios sean realistas, educativos y seguros.

### P: ¿Qué hago si quiero contribuir pero no soy un programador experimentado?
**R**: Hay muchas formas de contribuir sin escribir código:
- Mejora y traducción de documentación
- Sugerencia de mejoras de usabilidad o experiencia de usuario
- Reportes de problemas con pasos claros para reproducir
- Sugerencia de nuevos recursos o referencias de aprendizaje
- Ayuda en la organización y estructuración de la información
- Diseño de diagramas o ilustraciones para explicar conceptos

### P: ¿Cuánto tiempo suele tomar el proceso de revisión?
**R**: Depende de la complejidad de la contribución:
- Cambios menores de documentación: 1-3 días hábiles
- Correcciones de errores simples: 3-5 días hábiles
- Nuevas características medias: 1-2 semanas
- Cambios mayores o arquitéctonicos: 3-4 semanas o más
Le mantendremos informado del progreso en cada etapa.

### P: ¿Qué pasa si mi pull request es rechazado?
**R**: Un rechazo no significa que su contribución no sea valiosa. Las razones comunes para rechazo incluyen:
- Duplicación de trabajo ya en progreso o recientemente fusionado
- Desalineación con la dirección o prioridades actuales del proyecto
- Problemas técnicos significativos que requerirían una reestructuración mayor
- Falta de suficiente contexto o información para evaluar adecuadamente
- Problemas de seguridad o calidad que no pueden resolverse dentro del alcance de la contribución

Le proporcionaremos retroalimentación detallada para que pueda mejorar su contribución o entender por qué no encaja en este momento.

## Código de Conducta

Por favor revise y respete el `CODE_OF_CONDUCT.md` para mantener un ambiente de colaboración respetuoso y productivo.

---

*Última actualización: Agosto 2024*
*BlackForge Labs - Todos los derechos reservados*