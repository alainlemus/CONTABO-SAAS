# ContaboSaaS

Software de contabilidad multi-tenant para despachos mexicanos. Construido con Laravel 12 + Filament v5 + Laravel Cashier (Stripe).

---

## Stack

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.4 · Laravel 12 |
| Panel admin | Filament v5 |
| Frontend público | Blade puro (sin Livewire ni Vite en landing) |
| Pagos | Laravel Cashier · Stripe |
| Tests | PHPUnit 11 |
| Servidor local | Laravel Herd |
| Estilos | Tailwind (panel Filament) · CSS inline (landing/emails) |

---

## Arquitectura multi-tenant

El modelo de tenancy es **por usuario admin**. Cada contador que se registra es un "despacho" independiente. Sus clientes, facturas y obligaciones son solo visibles para él y su equipo.

- `User` con `role = admin` → es el dueño del despacho (el "tenant")
- `User` con `role = capturista | viewer` → miembro del equipo, con `owner_id` apuntando al admin
- `Client`, `FiscalObligation`, `Invoice` tienen un `GlobalScope` que filtra automáticamente por `user_id = auth()->user()->ownerId()`

### Roles

| Rol | Crear | Editar | Eliminar | Equipo |
|---|---|---|---|---|
| Admin | ✓ | ✓ | ✓ | ✓ |
| Capturista | ✓ | ✓ | ✗ | ✗ |
| Viewer | ✗ | ✗ | ✗ | ✗ |

---

## Modelos

### `User`
- Campos: `name`, `email`, `password`, `role` (enum), `owner_id`, `is_active`, `trial_ends_at`
- Traits: `Billable` (Cashier), `Notifiable`
- Implements: `FilamentUser`
- Relaciones: `owner()`, `teamMembers()`, `clients()`

### `Client`
- Expediente fiscal completo del cliente del despacho
- Campos principales: `tax_id` (RFC), `person_type`, `tax_regime` (clave SAT), `curp`, `legal_rep_name/rfc`, `email`, `phone`, `address`, `portal_sat_user/password`, `efirma_cer_path`, `efirma_key_path`, `documents` (JSON), `compliance_level`, `status`, `billing_cycle`
- GlobalScope filtra por `user_id` del admin autenticado
- Relaciones: `owner()`, `notes()`, `invoices()`, `fiscalObligations()`

### `FiscalObligation`
- Una obligación fiscal = un tipo + un período + un cliente
- Campos: `client_id`, `type` (enum), `period_year`, `period_month`, `due_date`, `status` (enum), `presented_at`, `reference`, `notes`, `acuse_pdf_path`
- GlobalScope filtra via `client.user_id`
- Métodos: `isOverdue()`, `acusePdfExists()`, `periodLabel()`

### `Invoice`
- Factura CFDI importada desde XML
- Campos: `uuid`, `serie`, `folio`, `fecha_emision`, `rfc_emisor/receptor`, `nombre_emisor/receptor`, `uso_cfdi`, `tipo_comprobante`, `metodo_pago`, `forma_pago`, `moneda`, `subtotal`, `descuento`, `iva`, `isr_retenido`, `iva_retenido`, `total`, `concepto_principal`, `xml_path`, `pdf_path`, `status`, `parse_error`

### `ClientNote`
- Notas internas por cliente
- Campos: `client_id`, `content`, timestamps

---

## Enums

### `ObligationType`
9 tipos de obligaciones fiscales SAT:
- Mensuales: `ISR Mensual`, `IVA Mensual`, `DIOT`
- Bimestrales: `ISR Bimestral`, `IVA Bimestral`, `IMSS Bimestral`
- Anuales: `ISR Anual`, `Declaración Anual PF`, `Declaración Anual PM`

Método `forRegime(string $taxRegime)` mapea cada clave SAT a sus obligaciones:

| Clave | Régimen | Obligaciones |
|---|---|---|
| 601 | General PM | ISR, IVA, DIOT, Anual PM |
| 603 | Fines no lucrativos | ISR, Anual PM |
| 605 | Sueldos y Salarios | Anual PF |
| 606 | Arrendamiento | ISR, IVA, Anual PF |
| 612 | Act. Empresariales PF | ISR, IVA, DIOT, Anual PF |
| 621 | RIF | ISR Bim, IVA Bim, Anual PF |
| 625 | Plataformas Tech | ISR, IVA, Anual PF |
| 626 | RESICO | ISR, IVA, Anual PF |

### `ObligationStatus`
`pending` · `presented` · `not_applicable` · `overdue`

### `UserRole`
`admin` · `capturista` · `viewer`

---

## Panel Filament (`/admin`)

### Resources

#### ClientResource
- CRUD completo de clientes con expediente fiscal
- Columnas: nombre, RFC, régimen, estado, ciudad, nivel de cumplimiento
- Filtros: régimen, tipo persona, estatus, ciudad
- Búsqueda global
- Relation Managers: `FiscalObligationsRelationManager`, `InvoicesRelationManager`, `NotesRelationManager`
- Vista detalle (`ViewClient`) con todas las secciones del expediente

#### FiscalObligationResource
- CRUD de obligaciones fiscales
- Columnas: cliente, tipo, período, fecha límite, estatus (badge de color)
- Filtros: estatus, tipo, período
- Acción **"Marcar como presentada"**: cambia estatus, guarda `presented_at`, permite adjuntar PDF del acuse, envía correo `ObligationPresentedMail`
- Exportación a CSV/Excel con `FiscalObligationExporter`

#### InvoiceResource
- CRUD de facturas CFDI
- Columna de tipo (emitida/recibida), UUID, emisor, receptor, total, fecha
- Acción **"Importar XML"** (`ImportarXmlAction`): parsea el XML CFDI con `XmlCfdiParser` y pre-llena todos los campos automáticamente
- Descarga de XML y PDF adjunto

#### TeamMemberResource
- CRUD de miembros del equipo (solo visible para admins)
- Campos: nombre, email, contraseña, rol, activo
- Solo el admin puede gestionar su propio equipo

### Pages

#### Dashboard
- Widgets: `DashboardStatsWidget`, `ObligacionesPorEstatusChartWidget`, `IngresosEgresosChartWidget`, `ProximasObligacionesWidget`, `CalendarioVencimientosWidget`, `TrialBannerWidget`

#### BillingPage (`/admin/billing`)
- Muestra plan activo, fechas de trial y suscripción
- Botones: suscribirse (checkout Stripe), portal de facturación, cancelar
- Oculta secciones de pago si no hay suscripción activa

### Widgets

| Widget | Descripción |
|---|---|
| `DashboardStatsWidget` | Contadores: clientes activos, obligaciones pendientes, vencidas, facturas del mes |
| `ObligacionesPorEstatusChartWidget` | Gráfica de dona con distribución por estatus |
| `IngresosEgresosChartWidget` | Gráfica de barras de ingresos vs egresos por mes |
| `ProximasObligacionesWidget` | Tabla de obligaciones próximas a vencer |
| `CalendarioVencimientosWidget` | Vista de calendario con obligaciones por fecha |
| `TrialBannerWidget` | Banner de aviso de trial con días restantes (se oculta si tiene suscripción activa) |

---

## Servicios

### `FiscalObligationGenerator`
- `generateForClient(Client, int $year, int $month)` — genera obligaciones del mes dado según el régimen SAT del cliente, sin duplicar
- `generateYearForClient(Client, int $year)` — genera el año completo (los 12 meses + anuales)
- `markOverdue()` — actualiza a `overdue` todas las obligaciones pendientes con fecha pasada
- Lógica de periodicidad: mensuales (vence día 17 mes siguiente), bimestrales (meses impares), anuales (marzo PM / abril PF)

### `XmlCfdiParser`
- Parsea archivos XML de CFDI 4.0
- Extrae: UUID, serie, folio, fecha emisión, RFC y nombre de emisor/receptor, uso CFDI, tipo comprobante, método y forma de pago, moneda, subtotal, descuento, IVA, ISR retenido, IVA retenido, total, concepto principal

---

## Comandos Artisan

| Comando | Descripción |
|---|---|
| `app:generate-monthly-obligations` | Genera obligaciones del mes actual para todos los clientes activos |
| `app:generate-annual-obligations` | Genera obligaciones anuales del año actual |
| `app:mark-overdue-obligations` | Marca como vencidas las obligaciones pendientes con fecha pasada |
| `app:notify-obligations-due-soon` | Envía correo `ObligationDueSoonMail` para obligaciones que vencen en los próximos 5 días |
| `app:notify-trial-ending-users` | Envía correo `TrialEndingMail` a usuarios con trial próximo a expirar |
| `app:send-test-emails` | Envía los 8 correos de prueba a `alainttlm@gmail.com` para verificar el diseño |

Todos los comandos periódicos deben programarse en `routes/console.php` (o con el scheduler de Laravel).

---

## Correos (Mailables)

| Mailable | Trigger | Descripción |
|---|---|---|
| `WelcomeMail` | Registro de usuario | Bienvenida con acceso al panel |
| `TrialEndingMail` | Comando `notify-trial-ending` | Aviso de que el trial expira pronto |
| `SubscriptionActivatedMail` | Webhook Stripe `customer.subscription.created` | Confirmación de suscripción activa |
| `PaymentSucceededMail` | Webhook Stripe `invoice.payment_succeeded` | Confirmación de pago exitoso |
| `PaymentFailedMail` | Webhook Stripe `invoice.payment_failed` | Aviso de pago fallido |
| `PaymentMethodUpdatedMail` | Webhook Stripe `customer.updated` | Confirmación de método de pago actualizado |
| `ObligationPresentedMail` | Acción "Marcar presentada" en Filament | Confirmación de obligación presentada |
| `ObligationDueSoonMail` | Comando `notify-obligations-due-soon` | Aviso de obligación próxima a vencer |

Todos los correos usan el layout compartido en `resources/views/components/emails/layout.blade.php` con logo, header de color configurable y footer estándar.

---

## Webhooks Stripe (Listeners)

| Evento Stripe | Listener | Acción |
|---|---|---|
| `customer.subscription.created` | `HandleSubscriptionActivated` | Envía `SubscriptionActivatedMail` |
| `invoice.payment_succeeded` | `HandlePaymentSucceeded` | Envía `PaymentSucceededMail` |
| `invoice.payment_failed` | `HandlePaymentFailed` | Envía `PaymentFailedMail` |
| `customer.updated` | `HandlePaymentMethodUpdated` | Envía `PaymentMethodUpdatedMail` |

---

## Suscripción (Stripe + Cashier)

- Flujo: Landing → `/register` → `/admin` (con trial) → `/subscription` → Stripe Checkout → `/subscription/success`
- Middleware `EnsureSubscribed`: redirige a `/subscription` si no hay trial activo ni suscripción
- `SubscriptionController`: maneja checkout, portal de facturación de Stripe, success y cancel
- Trial configurado en el registro (`trial_ends_at`)

---

## Tests (326 tests · 658 assertions)

| Archivo | Cobertura |
|---|---|
| `Auth/RegisterControllerTest` | Registro de usuario, validaciones, creación de trial |
| `ClientFileControllerTest` | Descarga de archivos CER/KEY |
| `FiscalObligationFileControllerTest` | Descarga de acuses PDF |
| `InvoiceFileControllerTest` | Descarga de XML/PDF de facturas |
| `EnsureSubscribedTest` | Middleware de suscripción |
| `RoutingTest` | Rutas públicas y protegidas |
| `Filament/ClientResourceTest` | CRUD completo de clientes |
| `Filament/FiscalObligationResourceTest` | CRUD y acción "marcar presentada" |
| `Filament/InvoiceResourceTest` | CRUD de facturas |
| `Filament/ImportarXmlActionTest` | Importación de XML CFDI |
| `Filament/TeamMemberResourceTest` | CRUD del equipo |
| `Filament/BillingPageTest` | Página de facturación |
| `Filament/DashboardWidgetTest` | Widgets del dashboard |
| `Filament/CalendarioVencimientosWidgetTest` | Widget de calendario |
| `Filament/TrialBannerWidgetTest` | Widget de trial |
| `Filament/GlobalSearchTest` | Búsqueda global |
| `Filament/FiscalObligationExporterTest` | Exportación CSV |
| `Filament/ObligationPresentedNotificationTest` | Correo al marcar presentada |
| `Filament/Clients/NotesRelationManagerTest` | Notas del cliente |
| `Filament/Clients/ViewClientTest` | Vista detalle del cliente |
| `Commands/GenerateMonthlyObligationsTest` | Comando generación mensual |
| `Commands/GenerateAnnualObligationsTest` | Comando generación anual |
| `Commands/MarkOverdueObligationsTest` | Comando marcar vencidas |
| `Commands/NotifyObligationsDueSoonTest` | Comando notificaciones |
| `Listeners/HandlePaymentFailedTest` | Listener webhook Stripe |
| `StripeWebhookTest` | Webhooks Stripe (eventos clave) |
| `StripeWebhookMailTest` | Correos disparados por webhooks (11 tests) |
| `Mail/WelcomeMailTest` | Correo de bienvenida |
| `Mail/TrialEndingMailTest` | Correo de trial |
| `Unit/Services/FiscalObligationGeneratorTest` | Lógica de generación de obligaciones |
| `Unit/Services/XmlCfdiParserTest` | Parser de XML CFDI |

### Ejecutar tests
```bash
php -d memory_limit=512M vendor/bin/phpunit --configuration phpunit.xml --no-coverage
```

---

## Assets / Branding

- `public/images/favicon.png` — favicon del sitio
- `public/images/contabo.png` — logo principal
- Panel Filament: favicon y brand logo configurados en `AdminPanelProvider`
- Landing y registro: favicon en `<head>` + logo imagen en `<nav>`
- Emails: logo con URL absoluta (`APP_URL/images/contabo.png`) — funciona en producción

---

## Despliegue (VPS con Dokploy)

Variables de entorno requeridas:

```env
APP_URL=https://tu-dominio.com
APP_ENV=production
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
CASHIER_CURRENCY=mxn

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="ContaboSaaS"
```

### Tareas programadas (cron)
Agregar al cron del servidor:
```bash
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```

Y en `routes/console.php` registrar:
```php
Schedule::command('app:generate-monthly-obligations')->monthlyOn(1, '06:00');
Schedule::command('app:generate-annual-obligations')->yearlyOn(1, 1, '06:00');
Schedule::command('app:mark-overdue-obligations')->dailyAt('00:05');
Schedule::command('app:notify-obligations-due-soon')->dailyAt('08:00');
Schedule::command('app:notify-trial-ending-users')->dailyAt('09:00');
```

---

## Ideas y mejoras pendientes

Ver sección **"Roadmap de mejoras"** más abajo.
