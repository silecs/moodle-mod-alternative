# Module "Alternatives"


* Module respectant le format Moodle2 (gestion automatique des versions, etc)
* Les textes seront écrits en anglais mais localisables.
  La traduction française sera fournie.
* Vocabulaire :
  un étudiant effectue un *choix* en sélectionnant certaines *options*.
  Autrement dit, chaque instance du module "Alternatives" est composée d'*options*
  auxquelles on peut s'inscrire.

## Développements ultérieurs

Ce qui suit ne sera pas présent dans la première version livrée :

* **Choix ordonné de préférences sans quota.**
  Il sera par contre possible de ne pas imposer de quotas sur les options,
  et d'autoriser les choix de multiples options.
* **Inscription conditionnée à un code.**
* **Inscription par équipe.**
  La structure de la base de données prévoit déjà ce cas,
  mais l'interface utilisateur devra être fortement modifiée pour gérer ce cas.

## Pages du module

### Paramètres globaux d'une instance

* Titre : <input type="text" />
* Description : <textarea></textarea>
* <input type="checkbox" /> Inscription par équipe  
  de <input type="text" /> à <input type="text" /> personnes par option.
* <input type="checkbox" /> Inscriptions multiples  
  de <input type="text" /> à <input type="text" /> options par personne.
* <input type="checkbox" /> Inscriptions définitives  
  L'étudiant ne pourra pas modifier ses choix.  
  L'enseignant peut toujours modifier les choix des étudiants.
* <input type="checkbox" /> Envoi d'un message automatique à l'étudiant qui s'inscrit.
* <input type="checkbox" /> Affichage public des inscrits.
* <input type="checkbox" /> Affichage public des inscrits du même groupe.
* <input type="checkbox" /> Période d'ouverture  
  Début : <input type="text" />  
  Fin : <input type="text" />

### Ajouter une option

* Titre : <input type="text" />
* Description : <textarea></textarea>
* Date : <input type="text" /> (date unique ? texte libre pour permettre une période ?)
* Places : <input type="text" /> (un champ vide indique une absence de limite)
* <input type="checkbox" /> Option variable suivant le groupe.

### Configurer les options pour chaque groupe

Pour chaque groupe du cours, les champs sont pré-remplis avec les valeurs de l'option.

Pour chaque option et chaque groupe :

* <input type="checkbox" /> Non disponible pour ce groupe.
* Description : <textarea></textarea> 
  (est-ce utile que cette caractéristique d'une option puisse être surchargée pour chaque groupe ?)
* Date : <input type="text" />
* Places : <input type="text" />

### Liste des inscriptions (avec export CSV)

| Option | Places | Inscriptions | Places restantes | Inscrits (liens pour modification) |
|----|----|----|----|----|
| Conférence A | 12 | 10 | 2 | J. Martin, ... |
| Conférence B | 2 | 0 | 2 | |

### Liste des inscrits (avec export CSV)

| Nom | Prénom | Option choisie | Chef d'équipe | Modifier |
|----|----|----|----|----|
| Hugo | Victor | Conférence A | | Modifier |
| Martin | Jean | Projet A | Jacques Dupont | Modifier |
| Martin | Jean | Conférence B | | Modifier |

### Liste des non-inscrits

| Nom | Prénom | Inscription |
|----|----|----|
| Martin | Jean | Inscrire |

### Affectation d'options à un étudiant (par un enseignant)

Liste des options avec pour chacune une case à cocher.

Les options n'ayant plus de place disponibles seront grisées et on ne pourra s'y inscrire.

Lors de l'enregistrement, vérification des contraintes (inscriptions multiples).
Éventuellement, envoi d'un message de confirmation à l'inscrit.

### Choix par l'étudiant

Même interface que ci-dessus (ou interface commune en partie, à discuter).

Refus des enregistrements si l'étudiant a déjà un choix
et que l'instance a le paramètre "Choix définitif" activé.

### Statistiques

...


## Capacités Moodle

Le comportement de certaines pages ou le droit d'accès dépendra des permissions
sur ces nouvelles capacités :

* `alternatives:register` : par défaut, attribué aux étudiants.
* `alternatives:viewregistrations` : par défaut, attribué aux enseignants non-éditeurs et +.
* `alternatives:forceregistrations` : par défaut, attribué aux enseignants éditeurs.


<style type="text/css">
table { border-collapse: collapse; }
th, td { border: 1px solid black; padding: 3px 2ex 3px 1ex; }
</style>
