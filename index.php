<?php

// Définition des en-têtes pour l'API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Définir les messages multilingues
$messages = [
    "client_not_found" => [
        "en" => "Client file not found.",
        "fr" => "Fichier clients.json non trouvé."
    ],
    "pdf_not_found" => [
        "en" => "Invoice PDF not found.",
        "fr" => "Fichier invoice.pdf non trouvé."
    ],
    "upload_error" => [
        "en" => "Error uploading file.",
        "fr" => "Erreur lors du téléchargement du fichier."
    ],
    "no_file_uploaded" => [
        "en" => "No file uploaded.",
        "fr" => "Aucun fichier téléchargé."
    ],
    "invalid_request" => [
        "en" => "Invalid request.",
        "fr" => "Requête invalide."
    ],
    "method_not_allowed" => [
        "en" => "Method not allowed.",
        "fr" => "Méthode non autorisée."
    ],
];

// Définir la langue par défaut
$language = 'en'; // Anglais par défaut

// Vérifier si le paramètre 'lang' est présent dans la requête
if (isset($_GET['language']) && in_array($_GET['language'], ['en', 'fr'])) {
    $language = $_GET['language'];
}

// Récupérer l'URI de la requête
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if ($requestUri == '/api/clients') {
            getClients();
        } elseif ($requestUri == '/api/pdf') {
            downloadInvoice();
        } else {
            sendResponse(400, ["message" => $messages["invalid_request"][$language]]);
        }
        break;

    case 'POST':
        if ($requestUri == '/api/upload') {
            uploadFile();
        } else {
            sendResponse(400, ["message" => $messages["invalid_request"][$language]]);
        }
        break;

    default:
        sendResponse(405, ["message" => $messages["method_not_allowed"][$language]]);
        break;
}

// Fonction pour récupérer des clients fictifs
function getClients() {
    global $messages, $language;
    $jsonFile = 'clients.json';
    if (file_exists($jsonFile)) {
        $clients = file_get_contents($jsonFile);
        sendResponse(200, json_decode($clients));
    } else {
        sendResponse(404, ["message" => $messages["client_not_found"][$language]]);
    }
}

// Fonction pour télécharger un fichier PDF fictif
function downloadInvoice() {
    global $messages, $language;
    $pdfFile = 'invoice.pdf';
    if (file_exists($pdfFile)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice.pdf"');
        readfile($pdfFile);
        exit;
    } else {
        sendResponse(404, ["message" => $messages["pdf_not_found"][$language]]);
    }
}

// Fonction pour uploader un fichier et afficher ses informations
function uploadFile() {
    global $messages, $language;
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];

        $destinationPath = 'uploads/' . $fileName;

        // Déplacer le fichier téléchargé vers le dossier de destination
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            // Lire le contenu du fichier
            $fileContent = file_get_contents($destinationPath);

            // Log des informations du fichier
            $logData = [
                "fileName" => $fileName,
                "fileType" => $fileType,
                "fileSize" => $fileSize,
                "fileContent" => $fileContent
            ];
            sendResponse(200, $logData);
        } else {
            sendResponse(500, ["message" => $messages["upload_error"][$language]]);
        }
    } else {
        sendResponse(400, ["message" => $messages["no_file_uploaded"][$language]]);
    }
}

// Fonction pour envoyer une réponse au format JSON
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data);
}

?>

