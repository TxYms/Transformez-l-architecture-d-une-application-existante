# Transformez l'architecture d'une application existante

# Plot

Renote is an application that allows user to take and store notes.
In renote, a user can:
- create notes
- visualize notes
- define relationship between the notes
- define tags
- and associate a tag to a note.

## Install

1. Install Laravel's Herd:
https://laravel.com/docs/12.x/installation#installation-using-herd

This will install Php, Composer and Laravel. The project requires **PHP 8.4 or higher**.

2. Install node v22

Install node version manager (MVN).
On Windows you can use this distribution:
https://github.com/coreybutler/nvm-windows#readme


3. Clone this project

4. Copy `.env.example` to `.env`

5. Run `composer install` then generate new APP_KEY with `php artisan key:generate`

6. Run `npm i` and `npm run dev`

7. Run `php artisan migrate --seed`

   If you already had a database from the previous version, run `php artisan migrate:fresh --seed` instead
   (the `tags` migration now runs before `notes`).

8. Start Herd

9. Access to Herd link from your browser

You are setup!

The seeder creates a test account: `test@example.com` / `password`, with 5 tags and 10 notes.

Run the tests with `php artisan test`.

---

# API REST

Toutes les fonctionnalités de Renote sont disponibles via une API REST, utilisable par le front web, une application mobile ou un service externe.

- **URL de base** : `http://<votre-site-herd>.test/api/v1`
- **En-têtes** : `Accept: application/json` et `Content-Type: application/json`
- **Authentification** : token [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum), obtenu avec `register` ou `login`, puis envoyé dans l'en-tête `Authorization: Bearer <token>`

## Format des réponses

Toutes les réponses, succès comme erreurs, ont la même forme :

```json
{
  "status": "success",
  "message": "Note created.",
  "data": { "id": 12, "text": "Acheter du café", "tag": { "id": 3, "name": "courses" } }
}
```

| Code | Signification |
| --- | --- |
| 200 | Succès (y compris les suppressions, qui renvoient `data: null`) |
| 201 | Ressource créée |
| 401 | Token absent, invalide ou identifiants incorrects |
| 403 | Ressource appartenant à un autre utilisateur |
| 404 | Ressource ou route introuvable |
| 405 | Verbe HTTP non autorisé sur cette route |
| 422 | Données invalides, le détail est dans `data.errors` |
| 429 | Trop de tentatives (connexion : 5 essais, mot de passe oublié : 6 par minute, API : 60 requêtes par minute) |
| 500 | Erreur serveur |

Exemple d'erreur de validation :

```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "data": { "errors": { "tag_id": ["The selected tag id is invalid."] } }
}
```

## Endpoints

| Méthode | URL | Description | Auth | Succès |
| --- | --- | --- | --- | --- |
| POST | `/auth/register` | Créer un compte (`name`, `email`, `password`, `password_confirmation`) | Non | 201 |
| POST | `/auth/login` | Se connecter (`email`, `password`) | Non | 200 |
| POST | `/auth/logout` | Révoquer le token courant | Oui | 200 |
| POST | `/auth/forgot-password` | Recevoir un lien de réinitialisation (`email`) | Non | 200 |
| POST | `/auth/reset-password` | Réinitialiser le mot de passe (`token`, `email`, `password`, `password_confirmation`) | Non | 200 |
| GET | `/user` | Profil de l'utilisateur connecté | Oui | 200 |
| PATCH | `/user` | Modifier `name` et/ou `email` | Oui | 200 |
| PUT | `/user/password` | Changer le mot de passe (`current_password`, `password`, `password_confirmation`) | Oui | 200 |
| DELETE | `/user` | Supprimer son compte (`password`) | Oui | 200 |
| GET | `/notes` | Lister ses notes, plus récentes d'abord (filtre optionnel `?tag_id=`) | Oui | 200 |
| POST | `/notes` | Créer une note (`text`, `tag_id`) | Oui | 201 |
| GET | `/notes/{id}` | Consulter une note | Oui | 200 |
| PUT | `/notes/{id}` | Modifier une note (`text`, `tag_id`) | Oui | 200 |
| DELETE | `/notes/{id}` | Supprimer une note | Oui | 200 |
| GET | `/tags` | Lister les tags (triés par nom) | Oui | 200 |
| POST | `/tags` | Créer un tag (`name`, unique, 50 caractères max) | Oui | 201 |

Le détail de chaque endpoint (règles de validation, exemples de requêtes et de réponses) est dans la documentation d'architecture.

## Exemple avec curl

```bash
# Connexion
curl -X POST http://renote.test/api/v1/auth/login \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Création d'une note avec le token reçu
curl -X POST http://renote.test/api/v1/notes \
  -H "Accept: application/json" -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"text":"Acheter du café","tag_id":3}'
```

## Organisation du code

```
app/Http/Controllers/Api/V1/   Contrôleurs : reçoivent la requête, appellent un service, renvoient la réponse
app/Http/Requests/             Validation des données entrantes
app/Http/Resources/            Format JSON des notes, tags et utilisateurs
app/Http/Responses/            Enveloppe { status, message, data }
app/Services/                  Logique métier
app/Repositories/              Accès aux données (interfaces + implémentations Eloquent)
app/Models/                    Modèles Eloquent
app/Policies/                  Autorisations (une note n'est accessible qu'à son auteur)
app/Exceptions/                Rendu JSON des erreurs de l'API
routes/api.php                 Routes de l'API
```

Le front Livewire existant reste fonctionnel : il utilise les mêmes services que l'API.
