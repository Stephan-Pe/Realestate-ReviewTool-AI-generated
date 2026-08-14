# Security Recommendations — Reviewtool

> Stand: 2025-06-24 | Projekt: Immobilien-Reviewtool (Graubünden)

---

## 1. Routing & URL Validation

### ✅ Implementiert: Whitelist-basierte URL-Validierung

In `public/index.php` wird jede eingehende URL vor dem Router-Durchlauf gegen eine Whitelist geprüft:

- **Exact matches** — exakte Pfade (z.B. `'homes/search'`)
- **Prefix matches** — Pfade mit ID-Parametern (z.B. `'homes/show/'` erlaubt `homes/show/123`)
- **Nicht whitelisted** → sofortiges 404, **kein Router, kein Controller, kein PHP-Code wird ausgeführt**

### 🔒 Wichtige Regel

> **Nie** `$_GET['url']`, `$_POST`, `$_GET` oder andere User-Inputs direkt an den Router, eine SQL-Abfrage oder eine Systemfunktion übergeben, ohne vorherige Validierung gegen eine Whitelist.

### 📝 Bei neuen Routen

Jede neue Route muss **an zwei Stellen** ergänzt werden:

1. **`public/index.php`** — in der `$allowedRoutes`-Whitelist (GET/POST/DELETE)
2. **`public/index.php`** — im Router (`$router->add(...)`)

```php
// 1. Whitelist ergänzen
$allowedRoutes['GET']['impressum'] = ['exact'];

// 2. Router ergänzen
$router->add('impressum', ['controller' => 'About', 'action' => 'impressum']);
```

---

## 2. CSRF Protection

### ✅ Implementiert

- `CsrfMiddleware` validiert bei **jeder POST-Anfrage** das CSRF-Token
- Token wird aus dem DOM-Input (`<input name="csrf_token">`) geholt
- Bei ungültigem Token → **Redirect mit Flash-Meldung**, kein Code wird ausgeführt

### 🔒 Best Practices

- **Kein GET für state-changing actions** — immer POST/PUT/DELETE verwenden
- **Token immer neu generieren** nach Login/Session-Regeneration
- **`SameSite=Strict`** für Session-Cookies ist konfiguriert

---

## 3. SQL Injection Prevention

### ✅ Implementiert

- **PDO Prepared Statements** werden durchgängig verwendet
- Alle User-Inputs werden über `bindParam()` oder positional parameters gesichert

### 🔒 Best Practices

- **Nie** User-Input direkt in SQL-Strings concatenaten
- **Stets** PDO prepared statements verwenden
- **Validierung** auf Eingabeseite (PLZ = 4 Ziffern, ID = `is_numeric()`)

```php
// ✅ Sicher
$stmt = $pdo->prepare("SELECT * FROM valuations WHERE plz LIKE ?");
$stmt->execute([$plz . '%']);

// ❌ UNSICHER
$sql = "SELECT * FROM valuations WHERE plz = '$plz'";
```

---

## 4. Input Validation & Sanitization

### ✅ Implementiert

- `Core\Validate` Klasse für server-side Validierung
- PLZ: regex check (`/^[0-9]{4}$/`)
- Fläche: `is_numeric()` + `> 0`
- PropertyType/Condition/Equipment: whitelist gegen erlaubte Werte

### 🔒 Best Practices

| Input-Typ | Validierung |
|---|---|
| PLZ (Postleitzahl) | Regex: `/^[0-9]{4}$/` |
| ID (numeric) | `filter_var($id, FILTER_VALIDATE_INT)` |
| Email | `filter_var($email, FILTER_VALIDATE_EMAIL)` |
| Text-Felder | `htmlspecialchars()` auf Output, Max-Länge auf Input |
| File Uploads | MIME-Type check, Size-Limit, Extension-Whitelist |
| JSON Body | `json_last_error() === JSON_ERROR_NONE` |

---

## 5. XSS (Cross-Site Scripting) Prevention

### ✅ Implementiert

- Twig Template Engine (`{{ }}`) **escapes automatisch** HTML
- `htmlspecialchars()` auf manuellen Outputs

### 🔒 Best Practices

- **Kein** `{!! !!}` (unescaped output) in Twig Templates verwenden
- **Kein** `innerHTML` mit User-Input in JavaScript
- **Content-Security-Policy** Header prüfen:

```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com;");
```

---

## 6. Session Security

### ✅ Implementiert

```php
session_set_cookie_params([
    'lifetime' => 0,           // bis Browser-Close
    'path'     => '/',
    'secure'   => true,        // nur HTTPS
    'httponly' => true,        // kein JS-Zugriff
    'samesite' => 'Strict',    // kein Cross-Site Sending
]);
```

### 🔒 Best Practices

- **Nach Login**: Session-ID regenerieren (`session_regenerate_id(true)`)
- **Logout**: Session komplett zerstören (`session_destroy()`)
- **Idle Timeout**: Session nach Inaktivität ablaufen lassen
- **Niemals** sensitive Daten in `$_SESSION` speichern (Passwörter, Kreditkarten)

---

## 7. Authentication & Authorization

### ✅ Implementiert

- `Auth::getUser()` prüft Session-Status
- `requireLogin()` Middleware für geschützte Routes
- Password Hashing mit `password_hash()` / `password_verify()`

### 🔒 Best Practices

- **Jede** Admin-Funktion muss `requireLogin()` + Role-Check haben
- **Rate Limiting** für Login-Endpoints implementieren
- **Password Policy**: Mindestens 12 Zeichen, Komplexität erzwingen
- **Brute-Force-Schutz**: Account-Lock nach 5 fehlgeschlagenen Versuchen

---

## 8. File Upload Security

### ⚠️ Aktuell Status

File Uploads werden im Projekt verwendet (Sales Images / Gallery).

### 🔒 Empfohlene Maßnahmen

| Maßnahme | Implementierung |
|---|---|
| **Extension Whitelist** | Nur `.jpg`, `.jpeg`, `.png`, `.webp` |
| **MIME-Type Check** | `finfo_file()` statt `$_FILES['file']['type']` |
| **Size Limit** | Max 5 MB pro File, 50 MB total |
| **Speicherort** | **Nicht** im Web-root, z.B. `/var/uploads/` |
| **Execution Block** | `.htaccess` mit `php_flag engine off` im Upload-Verzeichnis |
| **Random Filename** | `bin2hex(random_bytes(16)) . '.' . $ext` |
| **Image Resize** | `imagecreatefrom*()` + `imagejpeg()` für konsistente Größe |

---

## 9. Error Handling & Logging

### ✅ Implementiert

- `Core\Error::errorHandler()` fängt PHP-Fehler ab
- `Core\Error::exceptionHandler()` fängt Exceptions ab
- In **dev**: Full Stack Trace
- In **prod**: Generic error page, Stack Trace in Log

### 🔒 Best Practices

- **Niemals** Stack Traces oder DB-Fehler in der Browser-Ausgabe anzeigen (prod)
- **Error Log** regelmäßig prüfen (`/var/log/php_error.log`)
- **`display_errors = Off`** in prod `php.ini`
- **Sensitive Daten** (DB credentials, API keys) **nicht** in Logs

---

## 10. HTTP Headers & Configuration

### 🔒 Empfohlene Security Headers

```php
// In public/index.php oder .htaccess:

// XSS Protection
header("X-XSS-Protection: 1; mode=block");

// Clickjacking Protection
header("X-Frame-Options: DENY");

// Content-Type Sniffing Protection
header("X-Content-Type-Options: nosniff");

// HSTS (HTTPS Strict Transport Security)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Referrer Policy
header("Referrer-Policy: strict-origin-when-cross-origin");

// Permissions Policy
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
```

---

## 11. Database Security

### 🔒 Best Practices

| Maßnahme | Status |
|---|---|
| **PDO Prepared Statements** | ✅ Implementiert |
| **DB User mit minimalen Rechten** | 🔒 Konfigurieren |
| **PDO Error Mode = Exception** | 🔒 Prüfen |
| **Regular Backups** | 🔒 Automatisieren |
| **DB Connection in .env** | ✅ Implementiert |
| **SSL/TLS für DB Connection** | 🔒 Konfigurieren (prod) |

---

## 12. Dependency Security

### 🔒 Empfohlene Maßnahmen

```bash
# Regelmäßig prüfen auf bekannte CVEs
composer audit

# Dependencies aktuell halten
composer update

# Nicht benötigte Dependencies entfernen
composer remove <package>
```

- **`composer.lock`** committen (nicht `vendor/`)
- **Keine** dev-packages in prod (`composer install --no-dev`)
- **`autoload.php`** nicht direkt exposed

---

## 13. Checklist: Before Production Deploy

```
[ ] .env mit realen Credentials (keine Defaults)
[ ] APP_ENV=prod
[ ] display_errors = Off (php.ini)
[ ] Session Cookie: secure=true, httponly=true, samesite=Strict
[ ] HTTPS erzwingen (HSTS Header)
[ ] Alle neuen Routes in Whitelist ($allowedRoutes)
[ ] CSRF-Token in allen Forms vorhanden
[ ] Input Validation auf allen Endpoints
[ ] Error Pages: Generic (kein Stack Trace)
[ ] DB Backups automatisiert
[ ] File Upload: Extension + MIME + Size Check
[ ] composer audit — keine critical vulnerabilities
[ ] Rate Limiting auf Login/Signup
[ ] Admin-IP-Restriction (optional, für Admin-Panels)
```

---

## 14. Emergency Response

### Im Falle eines Security Incidents:

1. **Loggen prüfen** — `php_error.log`, `access.log`, DB logs
2. **Access Tokens revoken** — Session-ID regenerieren, DB tokens löschen
3. **Code revert** — Falls ein Commit das Problem引入了, reverten
4. **Patch erstellen** — Whitelist erweitern, Input Validation hinzufügen
5. **Post-Mortem** — Dokumentation der Lücke und der Gegenmaßnahme

### Kontakt / Escalation

| Level | Aktion |
|---|---|
| Low (Info-Leak, minor) | Nächster Sprint fixen |
| Medium (CSRF, XSS) | Nächste Woche fixen |
| High (Auth bypass, SQLi) | **Sofort** fixen, Notify Stakeholder |
| Critical (Active Exploit) | **Site down**, Incident Response Team |

---

## Appendix A: Route Whitelist Reference

### Current Whitelist (`public/index.php`)

**GET:**
- Exact: ``, `about`, `login`, `settings`, `signup`, `logout`, `homes/captcha`, `homes/list`, `homes/search`, `home/calculate`
- Prefix: `homes/show/`, `homes/editValuation/`, `homes/deleteValuation/`, `password/reset/`, `signup/activate/`, `admin/`

**POST:**
- Exact: `homes/search`, `homes/calculate`, `home/calculate`, `homes/save`, `homes/captcha`, `signup/captcha`

**DELETE:**
- Prefix: `homes/deleteValuation/`

---

## Appendix B: Quick Reference — Common Vulnerabilities

| Vulnerability | Cause | Fix |
|---|---|---|
| **SQL Injection** | Unescaped SQL strings | PDO prepared statements |
| **XSS** | Unescaped user output | Twig `{{ }}`, `htmlspecialchars()` |
| **CSRF** | Missing token on form | `CsrfMiddleware` on all POST |
| **Auth Bypass** | Missing `requireLogin()` | Middleware on every admin route |
| **Path Traversal** | User-controlled file paths | Whitelist + `realpath()` check |
| **SSRF** | User-controlled URLs | Block internal IPs, whitelist domains |
| **IDOR** | Direct object reference | Check ownership before access |
| **Mass Assignment** | Unfiltered input → model | Explicit field whitelist in save() |

---

*Diese Dokumentation wird bei jeder größeren Änderung aktualisiert.*
