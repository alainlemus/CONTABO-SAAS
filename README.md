# ContaboSaaS — Panel de Contabilidad para Despachos Mexicanos

> **README para agentes de IA.** Este documento describe el proyecto, sus decisiones de arquitectura, convenciones y estado actual. Léelo completo antes de hacer cualquier cambio.

---

## Contexto del negocio

SaaS de contabilidad dirigido a contadores independientes y pequeños despachos en México. El contador paga una suscripción mensual y desde el panel gestiona los clientes que atiende, sus facturas CFDI y sus obligaciones fiscales ante el SAT.

**No existe el concepto de "firma" o "despacho" como entidad separada.** El usuario administrador ES el despacho.

---

## Stack técnico

| Capa | Tecnología | Versión |
|---|---|---|
| Lenguaje | PHP | 8.4 |
| Framework | Laravel | 12 |
| Panel admin | Filament | 5.x |
| Base de datos | MySQL | — |
| Suscripciones | Laravel Cashier (Stripe) | ^16.3 |
| Tests | PHPUnit | 11 |
| Formato código | Laravel Pint | 1.x |
| Entorno local | Laravel Herd | — |

**URL local:** `https://contabo-saas-filament.test`  
**Panel:** `https://contabo-saas-filament.test/admin`

> Existe un segundo proyecto `contabo-saas` (Laravel + Livewire/Fortify) en `https://contabo-saas.test`. Está **completo y no se debe tocar**. Los errores LSP que provienen de ese proyecto son falsos positivos.

---

## Arquitectura y decisiones de diseño

### Multi-tenancy por scope

No se usa un paquete de multi-tenancy. La separación de datos se implementa con **Global Scopes de Eloquent**:

- `Client` → Global Scope inline en `booted()` que filtra `user_id = auth()->user()->ownerId()`
- `FiscalObligation` → Global Scope `owned` que filtra vía `whereHas('client', fn($q) => $q->where('user_id', ...))`
- La relación `FiscalObligation::client()` usa `->withoutGlobalScopes()` para evitar recursión

### Roles de usuario (`App\Enums\UserRole`)

| Rol | Valor | Permisos |
|---|---|---|
| `Admin` | `admin` | CRUD completo. Dueño de la cuenta. Paga Stripe. |
| `Capturista` | `capturista` | Crear y editar. No puede eliminar ni gestionar equipo. |
| `Viewer` | `viewer` | Solo lectura. |

Los capturistas y viewers son creados por un admin. Tienen `owner_id` apuntando al admin que los creó. El método `User::ownerId()` devuelve `owner_id ?? id` — útil para resolver siempre al admin responsable.

### Estructura de `users`

```
id, name, email, password, role (enum), owner_id (FK self), is_active,
trial_ends_at, stripe_id, pm_type, pm_last_four,    ← columnas de Cashier
created_at, updated_at
```

---

## Módulo de Suscripciones (Stripe / Cashier)

### Flujo

1. El admin se registra → recibe **14 días de trial gratis** (columna `trial_ends_at`).
2. Al vencer el trial, el middleware `EnsureSubscribed` bloquea el panel y redirige a `/subscription`.
3. Desde `/subscription` el admin inicia Stripe Checkout.
4. Stripe redirige a `/subscription/success` tras pago exitoso.
5. El admin puede gestionar su suscripción en el Portal de Stripe desde `/subscription/portal`.

### Middleware `EnsureSubscribed` (`App\Http\Middleware\EnsureSubscribed`)

- Registrado como alias `subscribed` en `bootstrap/app.php`.
- Aplicado en `AdminPanelProvider` dentro de `->authMiddleware([])`.
- Lógica: si el usuario no está autenticado → pasa. Si es admin → verifica `onTrial()` o `subscribed('default')`. Si es capturista/viewer → resuelve al `owner` y verifica su estado.
- Rutas de suscripción (`/subscription/*`) **no tienen** el middleware `subscribed` — están bajo solo `auth`.

### Configuración Cashier

- `config/cashier.php` → `currency = mxn`, `currency_locale = es_MX`
- `config/services.php` → `services.stripe.price_id` expone el `STRIPE_PRICE_ID`
- Nombre de suscripción: `'default'`
- El trial se inicia con `->trialDays(14)` al momento del checkout, no al registrarse

### Variables de entorno requeridas

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_ID=price_...
CASHIER_CURRENCY=mxn
CASHIER_CURRENCY_LOCALE=es_MX
```

---

## Modelos

### `User` (`app/Models/User.php`)
- Trait `Laravel\Cashier\Billable`
- Cast `trial_ends_at` → `datetime` (requerido para que `onTrial()` de Cashier funcione)
- Métodos: `isAdmin()`, `isCapturista()`, `isViewer()`, `ownerId()`
- Relaciones: `owner()`, `teamMembers()`, `clients()`

### `Client` (`app/Models/Client.php`)
- Pertenece a un `User` (admin) via `user_id`
- Global Scope filtra por `ownerId()` del usuario autenticado
- Campos fiscales: `tax_id`, `tax_regime`, `efirma_cer_path`, `efirma_key_path`

### `Invoice` (`app/Models/Invoice.php`)
- Pertenece a un `Client`
- Hereda el scope de tenancy a través del cliente
- Soporta importación de XML CFDI: `xml_path`, `pdf_path`, `folio`, `uuid`, etc.

### `ClientNote` (`app/Models/ClientNote.php`)
- Notas internas por cliente

### `FiscalObligation` (`app/Models/FiscalObligation.php`)
- Pertenece a un `Client`
- Global Scope `owned` (via `whereHas client`)
- La relación `client()` usa `->withoutGlobalScopes()` para evitar recursión
- Campos: `type` (enum `ObligationType`), `status` (enum `ObligationStatus`), `period`, `due_date`, `presented_at`, `acuse_pdf_path`

---

## Enums

### `ObligationType` — tipos de obligación fiscal
Mensual: `isr_mensual`, `iva_mensual`, `diot`  
Bimestral: `isr_bimestral`, `iva_bimestral`, `imss_bimestral`  
Anual: `isr_anual`, `declaracion_anual_pf`, `declaracion_anual_pm`

Incluye `forRegime(string $taxRegime): array` que dado un código de régimen SAT (ej. `'601'`) devuelve los tipos de obligación aplicables.

### `ObligationStatus` — estado de una obligación
`pending` (Pendiente), `presented` (Presentada), `not_applicable` (No aplica), `overdue` (Vencida)

---

## Filament Resources

Todos los resources están en `app/Filament/Resources/` con su namespace correspondiente.

| Resource | Directorio | Funciones clave |
|---|---|---|
| `ClientResource` | `Clients/` | CRUD clientes, subida de e.firma (CER/KEY), notas |
| `InvoiceResource` | `Invoices/` | CRUD facturas, importación XML CFDI, descarga PDF/XML |
| `TeamMemberResource` | `Team/` | CRUD equipo (solo admins), asigna rol |
| `FiscalObligationResource` | `FiscalObligations/` | CRUD obligaciones, generador automático, modal "marcar presentada", descarga acuse PDF |

### Particularidades de Filament v5

- `BulkActionGroup`, `DeleteBulkAction` → `Filament\Actions\{BulkActionGroup, DeleteBulkAction}`
- `Action`, `ActionGroup` → `Filament\Actions\{Action, ActionGroup}`
- En tablas: `->recordActions([])` y `->toolbarActions([])`
- `IconColumn` con `->visible(fn (Model $record))` falla — usar `->visible(fn (?string $state))`
- `ChartWidget::$heading` es propiedad de **instancia** (`protected ?string $heading`)
- `TableWidget::$heading` es propiedad **estática** (`protected static ?string $heading`)
- `Widget::$sort` es **estático** en todos los widgets (`protected static ?int $sort`)

---

## Widgets del Dashboard

| Widget | Clase | Descripción |
|---|---|---|
| Stats | `DashboardStatsWidget` | Tarjetas: total clientes, facturas del mes, obligaciones pendientes/vencidas |
| Gráfica | `ObligacionesPorEstatusChartWidget` | Barras por mes. Para `Presented` agrupa por `presented_at`; los demás por `due_date` |
| Tabla próximas | `ProximasObligacionesWidget` | Lista de obligaciones próximas a vencer |

---

## Controladores de descarga

Descargas de archivos privados (disco `local`, no `public`):

- `ClientFileController` → `GET /clients/{client}/download/cer` y `.../key`
- `InvoiceFileController` → `GET /invoices/{invoice}/download/xml` y `.../pdf`
- `FiscalObligationFileController` → `GET /fiscal-obligations/{fiscalObligation}/download/acuse`

Todos bajo middleware `auth`. La policy y el Global Scope previenen acceso cruzado entre admins.

---

## Vistas Blade

Solo existen para el módulo de suscripción (el panel lo maneja Filament):

```
resources/views/subscription/
├── index.blade.php    — Página de suscripción/paywall
├── success.blade.php  — Confirmación post-pago
└── cancel.blade.php   — Cancelación de checkout
```

---

## Rutas (`routes/web.php`)

```
GET  /                                      → welcome
GET  /subscription                          → subscription.index
POST /subscription/checkout                 → subscription.checkout
GET  /subscription/success                  → subscription.success
GET  /subscription/cancel                   → subscription.cancel
GET  /subscription/portal                   → subscription.portal
GET  /invoices/{invoice}/download/xml       → invoices.download.xml
GET  /invoices/{invoice}/download/pdf       → invoices.download.pdf
GET  /clients/{client}/download/cer         → clients.download.cer
GET  /clients/{client}/download/key         → clients.download.key
GET  /fiscal-obligations/{fo}/download/acuse → fiscal-obligations.download.acuse
```

---

## Servicios

### `FiscalObligationGenerator` (`app/Services/`)
Genera automáticamente las obligaciones fiscales de un cliente para un período dado, basándose en su régimen fiscal SAT (`ObligationType::forRegime()`).

---

## Políticas (Policies)

| Policy | Modelo |
|---|---|
| `ClientPolicy` | `Client` |
| `InvoicePolicy` | `Invoice` |
| `FiscalObligationPolicy` | `FiscalObligation` |

Siguen el patrón: admin puede todo dentro de su scope, capturista puede crear/editar pero no eliminar, viewer solo read.

---

## Tests

**159 tests, 347 assertions — todos pasando.**

```
tests/
├── Unit/
│   ├── Services/FiscalObligationGeneratorTest.php
│   └── Services/XmlCfdiParserTest.php
└── Feature/
    ├── ClientFileControllerTest.php
    ├── EnsureSubscribedTest.php          ← middleware de suscripción
    ├── FiscalObligationFileControllerTest.php
    ├── InvoiceFileControllerTest.php
    └── Filament/
        ├── ClientResourceTest.php
        ├── DashboardWidgetTest.php
        ├── FiscalObligationResourceTest.php
        ├── ImportarXmlActionTest.php
        ├── InvoiceResourceTest.php
        └── TeamMemberResourceTest.php
```

### Comandos de test

```bash
# Suite completa
php artisan test --compact

# Un archivo
php artisan test --compact tests/Feature/EnsureSubscribedTest.php

# Por nombre de test
php artisan test --compact --filter=test_admin_on_generic_trial_can_pass
```

### Convención en factories

`UserFactory` crea admins con `trial_ends_at = now()->addDays(14)` por defecto, simulando el estado real de un admin recién registrado. Los tests de `EnsureSubscribedTest` que prueban el caso "sin acceso" especifican `trial_ends_at = null` explícitamente.

---

## Convenciones de código

- **Pint** debe correr sobre todos los archivos PHP modificados antes de finalizar:
  ```bash
  php /Users/alainlemusmunoz/Herd/contabo-saas-filament/vendor/bin/pint archivo1.php archivo2.php --format agent
  ```
- **Cada cambio debe tener test.** PHPUnit, no Pest.
- No crear archivos de documentación sin pedirlo explícitamente.
- No usar `cd` en bash — usar parámetro `workdir` o rutas absolutas.
- `mkdir` falla en bash en esta máquina — usar Python: `python3 -c "import os; os.makedirs('ruta', exist_ok=True)"`
- Preferir Write de archivo completo sobre patches parciales cuando sea posible.

---

## Próximos pasos sugeridos

Lo que aún no está implementado:

- **Webhook de Stripe** — manejar eventos `customer.subscription.deleted`, `invoice.payment_failed`, etc. para actualizar estado en BD.
- **Registro con trial automático** — al registrarse, iniciar el trial genérico (`trial_ends_at`) directamente en la creación del usuario, sin esperar al checkout.
- **Notificaciones por email** — aviso cuando el trial está por vencer, cuando el pago falla, etc.
- **Seeders** — datos de prueba para demostración.
- **Módulo de reportes** — exportación de obligaciones y facturas a Excel/PDF.
