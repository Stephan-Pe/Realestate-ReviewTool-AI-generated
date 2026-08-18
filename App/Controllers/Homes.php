<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \Core\Validate;
use \App\Flash;
use \DateTime;
use \DateTimeZone;
use \App\Models\Home;
use \Core\Helper;
use \Core\ContactMailService;
use \App\Middleware\CaptchaMiddleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Homes Controller
 * Handles both the original sales/gallery features
 * and the new Real Estate Review (Bewertung) features.
 *
 * PHP version 8.2.12
 */
class Homes extends \Core\Controller
{
    protected const BASE_PATH = __DIR__ . '/../..';
    public $user;

    /**
     * Before filter
     */
    protected function before()
    {
        $this->user = Auth::getUser();
    }

    /**
     * After filter
     */
    protected function after()
    {
        // no-op
    }

    // ====================================================================
    //  ORIGINAL ACTIONS (Sales / Gallery) — kept for backward compat
    // ====================================================================

    public function indexAction()
    {
        $slides = Home::getsalesImagesForAdmin();
        $homes = Home::getAll();

        // Check for calculation result stored in session (from form POST)
        $calcResult = null;
        if (isset($_SESSION['_calc_result'])) {
            $calcResult = $_SESSION['_calc_result'];
            unset($_SESSION['_calc_result']);  // consume it
        }

        $date = new DateTime('today', new DateTimeZone('Europe/Zurich'));
        View::renderTemplate('Home/index.html', [
            'date' => $date,
            'slides' => $slides,
            'homes' => $homes,
            '_calc_result' => $calcResult,
        ]);
    }

    public function captchaAction()
    {
        Helper::outputCaptchaImage();
        exit;
    }

    /**
     * Show a single valuation (GET)
     * GET /homes/show/{id}
     */
    public function showAction()
    {
        $id = (int) $this->route_params['id'];
        $valuation = Home::findByID($id);

        if (!$valuation) {
            Flash::addMessage('Bewertung nicht gefunden.', Flash::WARNING);
            $this->redirect('/');
        }

        View::renderTemplate('Home/show.html', [
            'id' => $id,
            'valuation' => $valuation,
        ]);
    }

    public function newAction()
    {
        $this->requireLogin();
        View::renderTemplate('Home/new.html');
    }

    public function galleryAction()
    {
        $this->requireLogin();
        View::renderTemplate('Home/gallery.html');
    }

    /**
     * Update valuation (PUT or POST)
     * PUT /homes/update/{id}
     */
    public function updateAction()
    {
        // Accept both JSON body and form POST
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?? $_POST;
        } else {
            $data = $_POST;
        }

        $id = (int) $this->route_params['id'];
        $valuation = new Home($data);
        $valuation->id = $id;

        $result = $valuation->update();

        if ($result) {
            // For form POST, redirect to show page
            if (strpos($contentType, 'application/json') === false) {
                Flash::addMessage('Bewertung aktualisiert!', Flash::SUCCESS);
                $this->redirect('/homes/show/' . $id);
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true,
                'message' => 'Bewertung aktualisiert',
            ]);
            exit;
        } else {
            // For form POST, redirect back with error
            if (strpos($contentType, 'application/json') === false) {
                Flash::addMessage('Aktualisierung fehlgeschlagen: ' . implode(', ', $valuation->errors), Flash::WARNING);
                $this->redirect('/homes/edit/' . $id);
            }
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'errors' => $valuation->errors,
            ]);
            exit;
        }
    }

    /**
     * Edit valuation form (GET)
     * GET /homes/edit/{id}
     */
    public function editAction(): void
    {
        $id = (int) $this->route_params['id'];
        $valuation = Home::findByID($id);

        if (!$valuation) {
            Flash::addMessage('Bewertung nicht gefunden.', Flash::WARNING);
            $this->redirect('/');
        }

        View::renderTemplate('Home/edit.html', [
            'id' => $id,
            'valuation' => $valuation,
        ]);
    }



    /**
     * Delete valuation (DELETE or POST)
     * DELETE /homes/delete/{id}
     * POST  /homes/delete/{id}
     */
    public function deleteAction()
    {
        $id = (int) $this->route_params['id'];
        $result = Home::deleteByID($id);

        // For form POST, redirect with flash message
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['CONTENT_TYPE'] !== 'application/json') {
            if ($result) {
                Flash::addMessage('Bewertung gelöscht!', Flash::SUCCESS);
            } else {
                Flash::addMessage('Löschen fehlgeschlagen.', Flash::WARNING);
            }
            $this->redirect('/');
        }

        // For fetch/JSON requests, return JSON
        header('Content-Type: application/json; charset=utf-8');
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Bewertung gelöscht']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Löschen fehlgeschlagen.']);
        }
        exit;
    }


    public function sendcontactmailAction(): void
    {
        if (isset($_POST['captcha_value']) && !CaptchaMiddleware::checkCaptcha()) {
            Flash::addMessage('Captcha Fehler, bitte versuchen Sie es nochmal.');
            $this->redirect('/#contactForm');
        }
        $contact_name = $_POST['contact_name'] ?? '';
        $contact_firstname = $_POST['contact_firstname'] ?? '';
        $contact_phone = $_POST['contact_phone'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';
        $contact_subject = $_POST['contact_subject'] ?? '';

        $data = [
            'contact_name' => $contact_name,
            'contact_firstname' => $contact_firstname,
            'contact_phone' => $contact_phone,
            'contact_email' => $contact_email,
            'contact_subject' => $contact_subject,
        ];

        $obfuscated = $_POST['contact_recipient'] ?? '';
        $receiverEmail = str_replace('/at/', '@', $obfuscated);

        $mailService = new ContactMailService();
        if ($mailService->sendContactMessage($receiverEmail, $data)) {
            Flash::addMessage("Ihre Nachricht wurde erfolgreich gesendet.");
        } else {
            Flash::addMessage(
                "Fehler beim Senden der Nachricht. Bitte versuchen Sie es später erneut.",
                Flash::WARNING
            );
        }
        $this->redirect('/');
    }

    public function successAction()
    {
        View::renderTemplate('Home/success.html');
    }

    // ====================================================================
    //  REVIEW TOOL ACTIONS
    // ====================================================================

    /**
     * PLZ autocomplete search (POST /homes/search)
     * Returns JSON array of matching locations.
     */
    public function searchAction(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = Helper::getJsonInput();
        $query = $input['query'] ?? '';

        if ($query === '') {
            echo json_encode([]);
            exit;
        }

        $results = Home::searchLocations($query);
        echo json_encode($results);
        exit;
    }

    /**
     * Calculate valuation (POST /homes/calculate)
     * Returns JSON with calculated valuation data.
     */
    public function calculateAction(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = Helper::getJsonInput();

        $plz               = $input['plz'] ?? '';
        $propertyType      = $input['property_type'] ?? '';
        $area              = $input['area'] ?? 0;
        $condition         = $input['condition'] ?? '';
        $equipment         = $input['equipment'] ?? '';
        $residenceStatus   = $input['residence_status'] ?? 'erstwohnsitz';

        // Server-side validation
        if (empty($plz) || empty($propertyType) || empty($area) || empty($condition) || empty($equipment)) {
            http_response_code(400);
            echo json_encode(['error' => 'Ungültige PLZ oder fehlende Angaben']);
            exit;
        }

        $area = (float) $area;
        if ($area <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Wohnfläche muss grösser als 0 sein.']);
            exit;
        }

        $result = Home::calculateValuation($plz, $propertyType, $area, $condition, $equipment, $residenceStatus);

        echo json_encode(['success' => true, 'data' => $result]);
        exit;
    }

    /**
     * Save valuation (POST /homes/save)
     * Creates a new valuation record in the database.
     */
    public function saveAction(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $input = Helper::getJsonInput();
      

        $valuation = new Home($input);

        // Server-side validation
        $valuation->validate();
        if (!empty($valuation->errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'errors' => $valuation->errors]);
            exit;
        }

        $id = $valuation->save();

        if ($id) {
            echo json_encode([
                'success' => true,
                'message' => 'Bewertung gespeichert',
                'id' => $id,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Speichern fehlgeschlagen']);
        }
        exit;
    }

    /**
     * Get all valuations with optional search (GET /homes/list)
     * Supports query parameter ?search=term
     */
    public function listAction(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $search = $_GET['search'] ?? '';
        $valuations = Home::getAll($search);

        echo json_encode([
            'success' => true,
            'valuations' => $valuations,
            'total' => count($valuations),
        ]);
        exit;
    }

 

}
