# ITPC West Africa website 

````
cahier de route du developpement du website de ITPC West Africa. 
Notons que le site est développé comme suit: 
langage: PHP 8
Framework: symfony 6.1.*
```` 

reférence de [formattage](https://docs.github.com/fr/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax#links)

## Partie 1 : Backoffice 

### 1- Intégration du template du backoffice 

<details><summary> Détails </summary>
<p>

Le template du backoffice est un style menu vertical composé des rubriques suivants:

```Rubriques
- Tableau de bord
* Francais 
    - Slider
    - Type de présentation 
    - Présentation
    - Actulaités 
    - Campagnes 
    - Jobs
        - Appel d'offres
        - Candidats 
    - Ressources 
    - Galérie
* Anglais 
    - Slider
    - Présentation's type 
    - Presentation
    - News 
    - Campagnes 
    - Jobs
        - Tender
        - Candidates 
    - Resources 
    - Gallery
- Images
- Newsletter

* Administrateur 
    - Utilisateur
    - Monitoring 
```

Chaque rubrique est associée à une entité (table) d'où le MLD suivant:

```MLD
# Tables des rubriques française
FrSlider (Id, titre, media, statut) 
FrType (Id, libelle, slug)
FrPresentation (Id, titre, resumé, contenu, media, slug, tags, pageIndex, createdAt, updatedAt, #FrType)
FrActualite (Id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
FrCampagne (id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
FrJob (id, reference, titre, resume, contenu, media, slug, date fin, lieu fonction, createdAt, updatedAt)
FrRessource (id, reference, titre, description, media, slug, pageIdex, created, updatedAt)

#Table des rubriques anglaise 
EnSlider (Id, titre, media, statut) 
EnType (Id, libelle, slug)
EnPresentation (Id, titre, resumé, contenu, media, slug, tags, pageIndex, createdAt, updatedAt, #FrType)
EnActualite (Id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
EnCampagne (id, titre, resume, contenu, media, slug, tags, pageIndex, createdAt, updatedAt)
EnJob (id, reference, titre, resume, contenu, media, slug, date fin, lieu fonction, createdAt, updatedAt)
EnRessource (id, reference, titre, description, media, slug, pageIdex, created, updatedAt)

# tables globales
Candidat (id, nom, prenoms, phone, email, mediaLettreMotivation, mediaCV, createdAt, updatedAt, #job)
Visiteur (Id, IP, IdSession, page, createdAt)
page (id, TypePage, index)
User (id, username, pass, roles)
``` 
</p>
</details>

### 2- PHPSTAN
Installation du phpstan pour vérification du code PHP 
> composer require --dev phpstan/phpstan-symfony  
> 
> vendor/bin/phpstan analyse [option]

### 3- Gestion User
La rubrique User permet de gerer les utilisateurs du backoffice.
