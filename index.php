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
    "directory_transversal_not_allowed" => [
        "en" => "Directory transversal not allowed.",
        "fr" => "le changement de repertoire est interdit"
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

// Function to download a fictitious PDF file
function downloadInvoice() {
    global $messages, $language;

    // Ensure that the 'file_name' parameter is provided and safe
    if (isset($_GET['file_name'])) {
        $fileName = basename($_GET['file_name']); // Use basename to avoid directory traversal
        $pdfFile = "uploads/" . $fileName; // Correct concatenation with a dot operator

        // Check if the file exists
        if (file_exists($pdfFile)) {
            $baseDirectory = 'uploads/'; // Base directory path

            $realPath = realpath($baseDirectory . $fileName); // Get the real path
          
            if (strpos($realPath, $baseDirectory) !== false) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                readfile("uploads/" . $fileName);
            } else {
                sendResponse(404, ["message" => $messages["directory_transversal_not_allowed"][$language]]);
            }
            exit;
        } else {
            sendResponse(404, ["message" => $messages["pdf_not_found"][$language]]);
        }
    } else {
        sendResponse(400, ["message" => $messages["invalid_request"][$language]]);
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

