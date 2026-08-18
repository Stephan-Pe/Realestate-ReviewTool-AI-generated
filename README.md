# 🏠 Immobilien-Reviewtool

Ein modernes webbasiertes Reviewtool für Wohnimmobilien in der Schweiz, Focus auf den Kanton Graubuenden. Schätzen Sie den Marktwert Ihrer Immobilie basierend auf Standort, Zustand und Ausstattung. 

![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![SQLite](https://img.shields.io/badge/SQLite-3-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

---

## App location **/c/xampp/htdocs/Reviewtool**

---

## 📋 Inhaltsverzeichnis

- [Features](#-features)
- [Technologien](#-technologien)
- [Installation](#-installation)
- [Verwendung](#-verwendung)
- [API Dokumentation](#-api-dokumentation)
- [Datenbank](#-datenbank)
- [Bewertungslogik](#-bewertungslogik)
- [Struktur](#-projektstruktur)
- [Entwicklung](#-entwicklung)
- [FAQ](#-faq)

---

## ✨ Features

- **🔍 PLZ-Autocomplete** — Automatische Vervollständigung für deutsche und Schweizer Postleitzahlen mit Live-Suche
- **📊 Marktwertschätzung** — Berechnung des Immobilienwerts basierend auf mehreren Faktoren
- **💾 Bewertungen speichern** — Alle Bewertungen werden in einer MySQL-Datenbank gespeichert
- **📋 Bewertungsverlauf** — Übersicht aller durchgeführten Bewertungen mit Suchfunktion
- **✏️ Bearbeiten & Löschen** — Vollständiges CRUD für gespeicherte Bewertungen
- **📱 Responsives Design** — Optimiert für Desktop, Tablet und Mobilgeräte
- **🌍 Doppeltes Währungsformat** — Automatische Erkennung von DE (CH) und CH (CHF)
- **🔐 CSRF-Schutz** — Sichere Formularübertragung mit Token-Validierung

---

## 🛠 Technologien

| Schicht | Technologie |
|---------|-------------|
| **Backend** | PHP 8.2+ (PDO, MySQL) | Xampp v3.3.0 | 
| **Frontend** | HTML5, SCSS, Vanilla JavaScript | Gulp Bundler
| **Datenbank** | MySQL | Es wird eine review_tool.sql für den Import erstellt
| **Icons** | Font Awesome 6 |
| **Styling** | SCSS with CSS Custom Properties (Variables), Flexbox, Grid |
| **template engine** | Twig 3 | 
| **Security** | CSRF-Schutz, XSS-Protection |

```bash
composer require "twig/twig:^3.0"
```
---

## 📦 Installation

### Voraussetzungen

- PHP 8.2 oder höher
- MySQL Datenbank
- Webserver (Apache/Nginx) oder PHP eingebauter Server

### Schritt 1: Repository klonen oder herunterladen

```bash
cd /Users/YourAccount/
git clone <repository-url> Reviewtool
cd Reviewtool
```

### Schritt 2: Datenbank initialisieren

Die Datenbank muss vor der Installation der Datenbank initialisiert werden. Es wird eine review_tool.sql für den Import der Daten zur Verfügung gestellt. 

```bash
# Datenbank-Datei anlegen, anschliessend .sql Daten importieren
mysql -u root -p
create database review_tool;
use review_tool;
```

### Schritt 3: vHost konfigurieren, unter Apache/conf/extra/httpd-vhosts.conf

```bash
<VirtualHost *:80>
    # DocumentRoot "C:/xampp/htdocs/reviewtool/public"
    ServerName reviewtool.test
    DirectoryIndex index.php
    Redirect permanent / https://reviewtool.test/
    # <Directory "C:/xampp/htdocs/reviewtool/public">
             
    #  </Directory>
</VirtualHost>

<VirtualHost *:443>
  # Enable HTTP/2 on this Vhost
    ServerName reviewtool.test
    ServerAlias reviewtool.test
    Protocols h2 h2c http/1.1

    DocumentRoot "C:/xampp/htdocs/Reviewtool/public"
  
    DirectoryIndex index.php
    <Directory "C:/xampp/htdocs/Reviewtool/public">
             
    </Directory>
     # Make certificat from .bat-file and install on your machine
    SSLEngine on
    SSLCertificateFile "crt/reviewtool.test/server.crt"
    SSLCertificateKeyFile "crt/reviewtool.test/server.key"
</VirtualHost>
```
Dieser Vhost-File konfiguriert den Apache Server, um das Reviewtool auf `https://reviewtool.test/` zu starten.

### Schritt 4: vHost URL im System eintragen

Notepad als Administrator öffnen und folgende Datei öffnen **Alle Dateien**:

C:\Windows\System32\drivers\etc\hosts

```
127.0.0.1 reviewtool.test

::1 reviewtool.test

```

### Schritt 5: SSL-Zertifikat erstellen

Unter xampp/apache einen Ordner crt erstellen und eine cert.conf datei erstellen:

```conf
[ req ]

default_bits        = 2048
default_keyfile     = server-key.pem
distinguished_name  = subject
req_extensions      = req_ext
x509_extensions     = x509_ext
string_mask         = utf8only

[ subject ]

countryName                 = Country Name (2 letter code)
countryName_default         = CH

stateOrProvinceName         = State or Province Name (full name)
stateOrProvinceName_default = GR

localityName                = Locality Name (eg, city)
localityName_default        = CHUR

organizationName            = Organization Name (eg, company)
organizationName_default    = SITEART

commonName                  = Common Name (e.g. server FQDN or YOUR name)
commonName_default          = reviewtool.test

emailAddress                = Email Address
emailAddress_default        = admin@reviewtool.test

[ x509_ext ]

subjectKeyIdentifier   = hash
authorityKeyIdentifier = keyid,issuer

basicConstraints       = CA:FALSE
keyUsage               = digitalSignature, keyEncipherment
subjectAltName         = @alternate_names
nsComment              = "OpenSSL Generated Certificate"

[ req_ext ]

subjectKeyIdentifier = hash

basicConstraints     = CA:FALSE
keyUsage             = digitalSignature, keyEncipherment
subjectAltName       = @alternate_names
nsComment            = "OpenSSL Generated Certificate"

[ alternate_names ]

DNS.1       = reviewtool.test
```
ausserdem benötigen wir eine make-cert.bat datei mit folgendem Inhalt:

```bat
@echo off
set /p domain="Enter Domain: "
set OPENSSL_CONF=../conf/openssl.cnf

if not exist .\%domain% mkdir .\%domain%

..\bin\openssl req -config cert.conf -new -sha256 -newkey rsa:2048 -nodes -keyout %domain%\server.key -x509 -days 3650 -out %domain%\server.crt

echo.
echo -----
echo The certificate was provided.
echo.
pause
```
Das erstellen des Certifikat kann mit dem Befehl `make-cert.bat` oder durch Doppelklick auf die Datei `make-cert.bat` gestartet.

### 

### Schritt 6: Anwendung starten

**Option A: PHP eingebauter Server**

```bash
php -S localhost:8000 -t Reviewtool/public/
```

**Option B: Über XAMPP/WAMP**

1. Kopieren Sie den `Reviewtool` Ordner in `htdocs/`
2. Starten Sie Apache in der XAMPP Control Panel
3. Öffnen Sie über vHost `https://reviewtool.test/` im Browser

---

## 🚀 Verwendung

### Neue Bewertung erstellen

1. Öffnen Sie die Startseite
2. Geben Sie die **Postleitzahl** ein (Autocomplete wird aktiv)
3. Wählen Sie die **Objektart** (Einfamilienhaus, Mehrfamilienhaus, Wohnung, etc.)
4. Geben Sie die **Wohnfläche** in m² ein
5. Wählen Sie den **Zustand** und die **Ausstattung**
6. Klicken Sie auf **"Bewertung durchführen"**

### Ergebnisse

Die Schätzung zeigt:
- **Preis pro m²** basierend auf der PLZ und Objektart
- **Gesamtwert** der Immobilie
- **Bewertungsspanne** (−10% bis +10% des Schätzwerts)
- **Landesspezifische Währung** (CH oder CHF)

### Bewertungsverwaltung

- Alle Bewertungen werden automatisch gespeichert
- Im Tab **"Bewertungen"** sehen Sie die vollständige Liste
- Nutzen Sie die **Suche** nach PLZ, Ort oder Objektart
- Klicken Sie auf 👁️ zum **Ansehen**, ✏️ zum **Bearbeiten** oder 🗑️ zum **Löschen**

---

## 📡 API Dokumentation PHP MVC, Grundlagen aus UDEMY online Kurs von [Dave Hollingworth](https://github.com/daveh)

### Endpunkte

Diese Anwendung kommuniziert über JSON-Payloads. Die PHP-MVC-Controller erfassen den Rohdatenstrom der Anfrage, dekodieren ihn in native PHP-Arrays/Entitäten und übergeben die Daten direkt an die Twig-View-Schicht, wo sie mittels `{% for %}`-Schleifen verarbeitet werden.

#### 1. Routing 

- public/index.php


    ```php
    $router->add('{controller}/{action}');
    $router->add('{controller}/{id:\d+}/{action}');
    $router->add('', ['controller' => 'Homes', 'action' => 'index']);
    $router->add('homes/edit/{id:\d+}', ['controller' => 'Homes', 'action' => 'edit']);

    ```
- Core/Router.php

    ```php
    <?php

    namespace Core;

    /**
     * Router
     * 
     * PHP version 8.2.12
     */

    class Router
    {
        /**
         * Assosiative array of routes
         * @var array
         */
        protected $routes = [];

        /**
         * Parameter for the matched  routes
         * @var array
         */
        protected $params = [];


        /**
         * Add Route to routing table
         *
         * @param string $route  The URL
         * @param array  $params Parameters (controller, action, etc.)
         *
         * @return void
         */
        public function add(string $route, array $params = [])
        {
            // Convert the route to a regular expression: escape forward slashes
            $route = preg_replace('/\//', '\\/', $route);

            // Convert variables e.g. {controller}
            $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

            // Convert variables with custom regular expressions e.g. {id:\d+}
            $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

            // Add start and end delimiters, and case insensitive flag
            $route = '/^' . $route . '$/i';

            $this->routes[$route] = $params;
        }

        /**
         * Get all the routes from the routing table
         * 
         * @return array
         */

        public function getRoutes()
        {
            return $this->routes;
        }

        /**
         * Match the route to the routes in the routing table, setting the $params
         * property if a route is found.
         *    $reg_exp = "/^(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/";
         * @param string $url The route URL
         *
         * @return boolean  true if a match found, false otherwise
         */

        public function getMatch(string $url): bool
        {
            foreach ($this->routes as $route => $params) {
                if (preg_match($route, $url, $matches)) {
                    foreach ($matches as $key => $match) {
                        if (is_string($key)) {
                            $params[$key] = $match;
                        }
                    }

                    $this->params = $params;
                    return true;
                }
            }

            return false;
        }


        /**
         * Get the currently matched parameters
         *
         * @return array
         */
        public function getParams()
        {
            return $this->params;
        }

        /**
         * Dispatch the route, creating the controller object and running the
         * action method
         *
         * @param string $url The route URL
         *
         * @return void
         */
        public function dispatch($url)
        {
            $url = $this->removeQueryStringVariables($url);

            if ($this->getMatch($url)) {
                $controller = $this->params['controller'];
                $controller = $this->convertToStudlyCaps($controller);
                $controller = $this->getNamespace() . $controller;

                if (class_exists($controller)) {
                    $controller_object = new $controller($this->params);

                    $action = $this->params['action'];
                    $action = $this->convertToCamelCase($action);

                    if (preg_match('/action$/i', $action) == 0) {
                        $controller_object->$action();
                    } else {
                        throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
                    }
                } else {
                    //echo "Controller class $controller not found";
                    throw new \Exception("Controller class $controller not found");
                }
            } else {
                //echo 'No route matched.';
                throw new \Exception('No route matched.', 404);
            }
        }

        /**
         * Convert the string with hyphens to StudlyCaps,
         * e.g. post-authors => PostAuthors
         *
         * @param string $string The string to convert
         *
         * @return string
         */
        protected function convertToStudlyCaps($string)
        {
            return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        }

        /**
         * Convert the string with hyphens to camelCase,
         * e.g. add-new => addNew
         *
         * @param string $string The string to convert
         *
         * @return string
         */
        protected function convertToCamelCase($string)
        {
            return lcfirst($this->convertToStudlyCaps($string));
        }

        /**
         * Remove the query string variables from the URL (if any). As the full
         * query string is used for the route, any variables at the end will need
         * to be removed before the route is matched to the routing table. For
         * example:
         *
         *   URL                           $_SERVER['QUERY_STRING']  Route
         *   -------------------------------------------------------------------
         *   localhost                     ''                        ''
         *   localhost/?                   ''                        ''
         *   localhost/?page=1             page=1                    ''
         *   localhost/posts?page=1        posts&page=1              posts
         *   localhost/posts/index         posts/index               posts/index
         *   localhost/posts/index?page=1  posts/index&page=1        posts/index
         *
         * A URL of the format localhost/?page (one variable name, no value) won't
         * work however. (NB. The .htaccess file converts the first ? to a & when
         * it's passed through to the $_SERVER variable).
         *
         * @param string $url The full URL
         *
         * @return string The URL with the query string variables removed
         */
        protected function removeQueryStringVariables(string $url): string
        {
            if ($url != '') {
                $parts = explode('&', $url, 2);

                if (strpos($parts[0], '=') === false) {
                    $url = $parts[0];
                } else {
                    $url = '';
                }
            }

            return $url;
        }
        /**
         * Get the namespace for the controller class. The namespace defined in the
         * route parameters is added if present.
         *
         * @return string The request URL
         */
        protected function getNamespace()
        {
            $namespace = 'App\Controllers\\';

            if (array_key_exists('namespace', $this->params)) {
                $namespace .= $this->params['namespace'] . '\\';
            }

            return $namespace;
        }
    }
    ```

- Core/Model.php

    ```php
    <?php

    namespace Core;

    use PDO;
    use App\Config;

    /**
     * Base model
     *
     * PHP version 8.2.12
     */
    abstract class Model
    {
        public $user_id;
        /**
         * Get the PDO database connection
         *
         * @return mixed
         */
        protected static function getDB()
        {
            static $db = null;

            if ($db === null) {

                $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' .
                    Config::DB_NAME . ';charset=utf8mb4';
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
                // Throw an Exception when an Error occurs
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $db;
        }

        /**
         * Generate unique keys
         * 
         */
        public function randString($length = 8)
        {
            $characters = '0123456789';
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            return $randomString;
        }
    }

    ```
- Core/Controller.php

    ```php
    <?php

    namespace Core;

    use \App\Auth;
    use \App\Flash;

    /**
     * Base controller
     *
     * PHP version 8.2.12
     */
    abstract class Controller
    {

        /**
         * Parameters from the matched route
         * @var array
         */
        protected array $route_params = [];

        /**
         * Class constructor
         *
         * @param array $route_params  Parameters from the route
         *
         * @return void
         */
        public function __construct(array $route_params)
        {
            $this->route_params = $route_params;
        }

        /**
         * Magic method called when a non-existent or inaccessible method is
         * called on an object of this class. Used to execute before and after
         * filter methods on action methods. Action methods need to be named
         * with an "Action" suffix, e.g. indexAction, showAction etc.
         *
         * @param string $name  Method name
         * @param array $args Arguments passed to the method
         *
         * @return void
         */
        public function __call($name, $args)
        {

            $method = $name . 'Action';

            if (method_exists($this, $method)) {
                if ($this->before() !== false) {
                    call_user_func_array([$this, $method], $args);
                    $this->after();
                }
            } else {
                // echo "Method $method not found in controller " . get_class($this);
                throw new \Exception("Method $method not found in controller " .
                    get_class($this));
            }
        }


        /**
         * Before filter - called before an action method.
         *
         * @return void
         */
        protected function before()
        {
        }

        /**
         * After filter - called after an action method.
         *
         * @return void
         */
        protected function after()
        {
        }

        /**
         * Redirect to different page
         *
         * @param string $url The relative URL
         */
        protected function redirect($url)
        {

            header('Location: https://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
            exit;
        }

        /**
         * On restricted pages require the user to log in first.
         * Remeber the requested page to redirect him there
         * 
         * @return void
         */
        public function requireLogin()
        {
            if (!Auth::getUser()) {
                Flash::addMessage('Please login to access that page', Flash::INFO);
                Auth::rememberRequestPage();

                $this->redirect('/login');
            }
        }
    }
    ```

#### 1. Bewertung berechnen (ohne Speicherung)

```
POST /calculate
Content-Type: application/json
```

**Request Body:**

```json
{
    "plz": "6541",
    "property_type": "Einfamilienhaus",
    "area": 150,
    "condition": "renoviert",
    "equipment": "gehoben"
}
```

**Response (200 OK):**

```json
{
    "success": true,
    "data": {
        "plz": "6541",
        "location_name": "Moaesa",
        "country": "CH",
        "property_type": "Einfamilienhaus",
        "area": 150,
        "condition": "renoviert",
        "equipment": "gehoben",
        "price_per_sqm": 6500.00,
        "total_value": 975000.00,
        "location_factor": 1,
        "condition_factor": 1.10,
        "equipment_factor": 1.05
    }
}
```

**Response (Fehler):**

```json
{
    "error": "Ungültige PLZ oder fehlende Angaben"
}
```

---

#### 2. PLZ-Suche (Autocomplete)

```
POST /search
Content-Type: application/json
```

**Request Body:**

```json
{
    "query": "803"
}
```

**Response (200 OK):**

```json
[
    { "plz": "6541", "name": "Moaesa", "country": "CH" },
    { "plz": "6541", "name": "Moaesa", "country": "CH" },
    { "plz": "8001", "name": "Zürich", "country": "CH" }
]
```

---

#### 3. Alle Bewertungen abrufen

```
GET /list
```

**Query Parameters:**

| Parameter | Typ | Beschreibung |
|-----------|-----|--------------|
| `search` | string | Suchbegriff (PLZ, Ort, Objektart) |

**Response (200 OK):**

```json
{
    "valuations": [
        {
            "id": 1,
            "plz": "6541",
            "location_name": "Moaesa",
            "property_type": "Einfamilienhaus",
            "area": 150,
            "total_value": 975000.00,
            "created_at": "2025-01-15 14:30:00"
        }
    ],
    "total": 1
}
```

---

#### 4. Einzelne Bewertung abrufen

```
GET /show/{id}
```

**Response (200 OK):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "plz": "6541",
        "location_name": "Moaesa",
        "country": "DE",
        "property_type": "Einfamilienhaus",
        "area": 150,
        "condition": "renoviert",
        "equipment": "gehoben",
        "price_per_sqm": 6500.00,
        "total_value": 975000.00,
        "location_factor": 1.25,
        "condition_factor": 1.10,
        "equipment_factor": 1.05,
        "created_at": "2025-01-15 14:30:00",
        "updated_at": "2025-01-15 14:30:00"
    }
}
```

---

#### 5. Bewertung speichern

```
POST /save
Content-Type: application/json
```

**Request Body:**

```json
{
    "plz": "10115",
    "property_type": "Einfamilienhaus",
    "area": 150,
    "condition": "renoviert",
    "equipment": "gehoben",
    "price_per_sqm": 6500.00,
    "total_value": 975000.00,
    "location_factor": 1.25,
    "condition_factor": 1.10,
    "equipment_factor": 1.05
}
```

**Response (201 Created):**

```json
{
    "success": true,
    "message": "Bewertung gespeichert",
    "id": 1
}
```

---

#### 6. Bewertung aktualisieren

```
PUT /edit/{id}
Content-Type: application/json
```

**Request Body:**

```json
{
    "plz": "10115",
    "property_type": "Einfamilienhaus",
    "area": 150,
    "condition": "renoviert",
    "equipment": "gehoben",
    "price_per_sqm": 6500.00,
    "total_value": 975000.00,
    "location_factor": 1.25,
    "condition_factor": 1.10,
    "equipment_factor": 1.05
}
```

**Response (200 OK):**

```json
{
    "success": true,
    "message": "Bewertung aktualisiert"
}
```

---

#### 7. Bewertung löschen

```
DELETE /delete/{id}
```

**Response (200 OK):**

```json
{
    "success": true,
    "message": "Bewertung gelöscht"
}
```

---

## 🗄️ Datenbank

### Tabellenstruktur

#### `valuations` — Bewertungsdaten

| Spalte | Typ | Beschreibung |
|--------|-----|--------------|
| `id` | INTEGER PK | Primärschlüssel (auto-increment) |
| `plz` | TEXT | Postleitzahl |
| `location_name` | TEXT | Ortsname |
| `country` | TEXT | Land (`DE` oder `CH`) |
| `property_type` | TEXT | Objektart |
| `area` | REAL | Wohnfläche in m² |
| `condition` | TEXT | Zustand |
| `equipment` | TEXT | Ausstattung |
| `price_per_sqm` | REAL | Preis pro m² |
| `total_value` | REAL | Gesamtwert |
| `location_factor` | REAL | Standortfaktor |
| `condition_factor` | REAL | Zustandsfaktor |
| `equipment_factor` | REAL | Ausstattungsfaktor |
| `created_at` | TEXT | Erstellungszeitpunkt |
| `updated_at` | TEXT | Aktualisierungszeitpunkt |

### Indexes

- `idx_plz` — Schnelle PLZ-Suche
- `idx_location` — Schnelle Orssuche
- `idx_property_type` — Filterung nach Objektart
- `idx_created_at` — Chronologische Sortierung

---

## 🧮 Bewertungslogik

Die Bewertung basiert auf einem multiplikativen Faktor-Modell:

```
price_per_sqm = base_price × location_factor × condition_factor × equipment_factor
total_value = price_per_sqm × area
```

### Basispreise nach Objektart (CH)

| Objektart | Basispreis/m² |
|-----------|---------------|
| Einfamilienhaus | 8'188 CH |
| Mehrfamilienhaus | 6'772 CH |
| Wohnung | 9'026 CH |
| Reihenhaus | 7'400 CH |
| Doppelhaus | 7'900 CH |
| Grundstück | 1000 CH/m² (Baugebiet) |
| Landwirtschaftlich | 1.000 CH/ha |

### Zustandsfaktoren

| Zustand | Faktor |
|---------|--------|
| neuwertig | 1.20 |
| renoviert | 1.10 |
| gepflegt | 1.00 |
| sanierungsbedürftig | 0.85 |
| renovierungsbedürftig | 0.70 |

### Ausstattungsfaktoren

| Ausstattung | Faktor |
|-------------|--------|
| luxus | 1.30 |
| gehoben | 1.15 |
| standard | 1.00 |
| einfach | 0.85 |

### Standortfaktoren

Die Standortfaktoren werden aus der PLZ-Datenbank abgeleitet:

- **Großstädte** (Zuerich, Bern, Basel, Genf, etc.): 1.20–1.50
- **Mittelstädte**: 1.00–1.20
- **Kleinstädte/Land**: 0.70–0.95
- **Schweiz**: Separate Preisbasis mit lokalem Faktor

**Wichtig:** Als Grundlage für die Standortfaktoren Wurde ein `Location.md`erstellt, diese Daten sind als Standortfaktoren und locations für die zu erstellende .sql-Datei für die PLZ-Datenbank zu verwenden.

> ⚠️ **Hinweis:** Die Basispreise sind Richtwerte und dienen nur als ungefähre Orientierung. Für eine verbindliche Bewertung wenden Sie sich bitte an einen zertifizierten Gutachter.

---

## 📁 Projektstruktur

```
Reviewtool/
├── README.md                 # Diese Datei
├── composer.json             # PHP Abhängigkeiten (falls erweitert)
├── App/
│   ├── Auth.php           # Authentifizierung
│   ├── Flash.php          # Flash-Meldungen
│   ├── Config.php         # Konfigurationen
|   ├── Models/
|   │   ├── Home.php          # (extends Core\Model) Datenbank interaktionen (kann bei Bedarf erweitert werden, zum Beispiel About.php)
|   ├── Controllers/
│   |   ├── Homes.php         # (extends Core\Controller) Methoden für die Home- Seite indexAction, searchAction, |deleteAction etc.
|   ├── Views/
|   |   ├──/
|   |   ├── Home/          # alle Seiten der Home Route `/`
|   |   │   ├── index.html      # Home-Index-Seite
|   |   |   ├── search.html     # Suchseiten der Suche `/search`
|   |   |   └── weitere Seiten falls nötig
|   |   └── base.html       # Basisklasse für alle Seiten ( Twig template engine )
├── vendor/                    # Abhängigkeiten (falls erweitert)
├── public/
│   ├── index.php             # Definieren der Routes  **$router->add('{controller}/{action}');**
│   ├── css/
│   │   └── style.css         # Styling
│   └── js/
│       └── app.js            # Frontend-Logik
├── Core/
│   ├── Model.php           # Basisklasse für alle Models
│   └── Controller.php      # Basisklasse für alle Controllers
|__ public/
    ├── index.php             # Hauptanwendung (Router & Controller)
    ├── css/
    │   └── style.css         # Styling
    └── js/
        └── app.js            # Frontend-Logik
```

---

## 👨‍💻 Entwicklung

### Neue PLZ-Daten hinzufügen

Bearbeiten Sie die PLZ-Daten direkt in `Reviewtool/public/index.php` in der `$plzDatabase` Variable oder laden Sie externe Daten in das JSON-Format:

```php
$plzDatabase = [
    [
        "plz" => "1234",
        "name" => "Stadtname",
        "country" => "CH",
        "factor" => 1.15,
        "type" => "stadt"
    ],
    // ... weitere Einträge
];
```

### Eigene Basispreise anpassen

Passen Sie die Basispreise in der `Reviewtool/public/index.php` `calculateValuation()` Funktion an:

```php
$basePrices = [
    'Einfamilienhaus' => 4000,  // Neuer Basispreis
    // ...
];
```

### CSS Custom Properties erweitern

Die Design-Variablen sind in `Reviewtool/public/css/style.css` unter `:root` definiert:

```css
:root {
    --primary: #2563eb;
    --success: #16a34a;
    --danger: #dc2626;
    --radius: 12px;
    /* ... weitere Variablen */
}
```

---

## ❓ FAQ

### Wie wird der Wert berechnet?

Der Wert wird multiplikativ aus Basispreis, Standortfaktor, Zustandsfaktor und Ausstattungsfaktor berechnet. Alle Faktoren sind in der Bewertungslogik hinterlegt und können angepasst werden.

### Unterstützt das Tool andere Länder?

Aktuell wird nur die **die Schweiz** vorläufig der Kanton **Graubuenden** unterstützt. Weitere Länder können durch Hinzufügen von PLZ-Daten und Basispreisen erweitert werden.

### Wo werden die Daten gespeichert?

Alle Bewertungen werden in der lokalen SQLite-Datenbank `Reviewtool/data/app.db` gespeichert. Es ist keine externe Datenbank erforderlich.

### Ist das Tool production-ready?

Das Tool ist für den **Demonstrations- und Entwicklungszweck** konzipiert. Für den produktiven Einsatz werden folgende Erweiterungen empfohlen:
- Authentifizierung/Zugriffskontrolle
- Validierung und Input-Sanitization auf Serverseite
- Error Logging
- Caching für PLZ-Abfragen
- Backup-Funktionalität

### Kann ich die Bewertungen exportieren?

Exportfunktionen können leicht durch Hinzufügen einer `action=export` Option in `Reviewtool/public/index.php` erweitert werden (CSV/JSON).

---

## 📝 Lizenz

MIT License — Siehe [LICENSE](LICENSE) Datei für Details.

---

## 🤝 Contributing

1. Forken Sie das Repository
2. Erstellen Sie einen Feature Branch (`git checkout -b feature/feature-name`)
3. Committen Sie Ihre Änderungen (`git commit -am 'Add feature'`)
4. Pushen Sie zum Branch (`git push origin feature/feature-name`)
5. Öffnen Sie einen Pull Request

---

## 📧 Kontakt

Bei Fragen oder Anregungen öffnen Sie gerne ein Issue im Repository.

---

<div align="center">

**Made with ❤️ for Real Estate Professionals**

</div>
# Realestate-ReviewTool-AI-generated
