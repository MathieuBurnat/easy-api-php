# Introduction
Ce projet est un exemple afin de montrer à quoi ressemble la structure d'une API en php.

## Structure des fichiers
Ici vous trouverez la structure d'exemple :
```
/api/
|-- index.php          (point d'entrée de l'API)
|-- clients.json       (fichier JSON contenant des clients fictifs)
|-- uploads/           (dossier pour stocker les fichiers uploadés)
```

## API : Contenus

Pour récupérer des clients fictifs : Effectuez une requête GET sur `http://{ip}/api/clients`.

Pour télécharger un fichier PDF : Effectuez une requête GET sur `http://{ip}/api/pdf`.

Pour uploader un fichier : Effectuez une requête POST sur `http://{ip}/api/upload` avec un fichier. 
Utilisez un formulaire HTML ou un client API comme Postman.
