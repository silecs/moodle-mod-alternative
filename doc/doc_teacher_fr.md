# Module Alternative : documentation pour l'enseignant

## Présentation générale

L'activité *Alternative* permet aux étudiants de s'inscrire à un ou plusieurs
choix dans une liste donnée.

Elle peut s'appliquer à différents cas : choix de sujets de stages, réservation d'une place
dans une conférence, choix d'une date de soutenance, etc.

Le fonctionnement est proche du module officiel "Choice",
mais propose de multiples options pour s'adapter à des besoins variés :

- inscription individuelle ou par équipe (par un chef d'équipe)
- quota possible sur chaque choix
- choix unique, ou limité ou illimité
- saisie manuelle des choix ou importation d'un fichier csv
- statistiques
- export csv des inscriptions


## Fonctionnement

### Configuration par l'enseignant

Alternative est une activité normale.
Elle s'ajoute donc à un cours de la même façon que les autres.

En sus des champs communs à toutes les activités (nom, description, etc),
les indications spécifiques sont :
la modification autorisé, l'affichage compact ou détaillé, l'activation des
équipes, le contrôle des inscriptions multiples.
Chaque option est documentée dans l'aide Moodle (bouton "?").

Ensuite, vient la saisie des options, manuelle ou par import d'un fichier CSV.
Il y a deux champs prévus par défaut pour la saisie d'options, et un bouton permet
d'en ajouter 2, autant de fois que nécessaire.

#### Accès sous conditions
Pour que l'accès à une activité puisse être restreint à une période donnée,
il faut activer le paramètre "_enableavailability_" sur la page 
"_Administration du site » Fonctions avancées_".


### Administration par l'enseignant

Quand l'activité est créée, quatre ou cinq nouvelles entrées sont disponibles
dans le menu _Réglages » Administration d'Alternative_ pour les vues :

- Voir le bilan
- Voir les inscriptions
- Voir les équipes (si cette modalité est activée)
- Voir les utilisateurs inscrits
- Voir les utilisateurs non inscrits

#### Voir le bilan
Le bilan affiche les statistiques générales dans un tableau.
Les chiffres des dernières lignes sont actifs et mènent aux vues correspondantes.

À partir de cette vue, un lien et deux boutons permettent également à l'enseignant :

- d'effectuer un export csv des inscriptions,
- de forcer les inscriptions de certains étudiants (cf la vue étudiant),
- d'envoyer un message de relance aux étudiants du cours n'ayant pas encore fait leur choix.

#### Voir les inscriptions
Cette table liste les inscriptions, avec une option par ligne.
Les étudiants inscrits à une même option sont énumérés sur la même ligne.

#### Voir les équipes
Cette table liste les inscriptions, avec une équipe par ligne.
Chaque ligne détaille la composition de l'équipe, l'effectif, les options choisies,
et met en évidence le chef d'équipe.

#### Voir les utilisateurs inscrits
Cette table liste les inscriptions, avec un étudiant par ligne.
Dans le cas des choix multiples autorisés, toutes les options de l'étudiant
sont énumérées sur la même ligne.

#### Voir les utilisateurs non inscrits
Cette table liste les utilisateurs non inscrits.
Pour chacun, un bouton permet à l'enseignant de forcer l'inscription.


### Inscription par les étudiants

Dans le *mode inscription individuelle*, lorsque l'étudiant ouvre l'activité,
il peut s'inscrire simplement (ou changer ses inscriptions) à certaines options.

Dans le *mode inscription par équipe*, il peut s'inscrire lui-même ainsi que plusieurs
de ses coéquipiers au choix.
Dans ce cas, il sera désigné comme chef d'équipe.

