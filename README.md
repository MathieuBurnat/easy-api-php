# Introduction
## Structure des fichiers
/api/
|-- index.php          (point d'entrée de l'API)
|-- clients.json       (fichier JSON contenant des clients fictifs)
|-- invoice.pdf        (fichier PDF fictif)
|-- uploads/           (dossier pour stocker les fichiers uploadés)

## API : Contenus

Pour récupérer des clients fictifs : Effectuez une requête GET sur http://ip/api/clients.

Pour télécharger un fichier PDF : Effectuez une requête GET sur http://ip/api/pdf.

Pour uploader un fichier : Effectuez une requête POST sur http://ip/api/upload avec un fichier. 
Utilisez un formulaire HTML ou un client API comme Postman.
