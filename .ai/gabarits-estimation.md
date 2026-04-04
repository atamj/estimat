# Plan : Gabarits d'estimation

**Statut** : Done  
**Date de création** : 2026-04-04

## Objectif

Permettre la création de gabarits d'estimation réutilisables (ex: Landing Page, E-commerce, Site vitrine) avec des pages et blocs pré-configurés. On peut créer une estimation depuis un gabarit, ou sauvegarder une estimation existante comme gabarit.

Un gabarit fonctionne exactement comme une estimation, sauf qu'il n'a pas de nom de client ni de projet — seulement un nom de gabarit.

---

## Étape 1 — Base de données [x]

Créer 3 migrations + 1 table pivot :

### `templates`
Miroir de `estimations` sans `client_name` et `project_name`.
```
id, user_id, name, type (hour/fixed), setup_id, project_type_id, currency
translation_enabled, translation_type, translation_fixed_price,
translation_fixed_hours, translation_percentage, translation_languages_count
timestamps
```

### `template_pages`
Miroir de `pages`.
```
id, template_id, name, quantity, order, type (regular/header/footer)
timestamps
```

### `template_page_blocks`
Miroir du pivot `page_block`.
```
id, template_page_id, block_id, quantity, order
price_programming, price_integration, price_field_creation, price_content_management
timestamps
```

### `template_addon` (pivot)
Miroir de `estimation_addon`.
```
template_id, option_id
```

---

## Étape 2 — Modèles Eloquent [x]

### `Template`
Relations : `user()`, `pages()`, `regularPages()`, `headerPage()`, `footerPage()`, `setup()`, `projectType()`, `addons()`

### `TemplatePage`
Relations : `template()`, `blocks()`

### `TemplatePageBlock` (pivot custom)
Champs prix : `price_programming`, `price_integration`, `price_field_creation`, `price_content_management`

---

## Étape 3 — Livewire `TemplateBuilder` [x]

Composant quasi-identique à `EstimationBuilder`, avec ces différences :
- Pas de `client_name` / `project_name` → remplacé par `name`
- Pas de calcul de totaux (un gabarit n'a pas de prix finaux)
- Même logique sinon : `addPage`, `addBlockToPage`, `removeBlockFromPage`, `updateBlockField`, `movePage`, `moveBlock`, `toggleAddon`, etc.

Fichiers créés :
- `app/Livewire/TemplateBuilder.php`
- `resources/views/livewire/template-builder.blade.php`
- `resources/views/livewire/partials/template-builder-page.blade.php`
- `resources/views/templates/builder.blade.php`

---

## Étape 4 — Contrôleur & Routes [x]

### `TemplateController`
- `index` — liste des gabarits
- `create` / `store` — nouveau gabarit (nom, type, devise, type de projet)
- `builder` — page constructeur
- `destroy` — suppression
- `duplicate` — duplication
- `createEstimation($template)` — crée une estimation en copiant la structure du gabarit

### Ajout dans `EstimationController`
- `saveAsTemplate($estimation)` — crée un gabarit depuis une estimation existante

### Routes dans `routes/web.php`
```php
// Gabarits
Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
Route::get('/templates/{template}/builder', [TemplateController::class, 'builder'])->name('templates.builder');
Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
Route::post('/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
Route::post('/templates/{template}/create-estimation', [TemplateController::class, 'createEstimation'])->name('templates.create-estimation');

// Depuis une estimation existante
Route::post('/estimations/{estimation}/save-as-template', [EstimationController::class, 'saveAsTemplate'])->name('estimations.save-as-template');
```

---

## Étape 5 — Intégration UI [x]

### Page de création d'estimation (`estimations/create`)
- Section "Partir d'un gabarit" ajoutée en haut de la page
- Liste les gabarits de l'utilisateur avec nom, type de projet, nb de pages et blocs
- Cliquer sur un gabarit → `createEstimation()` copie les pages/blocs et redirige vers le builder

### Builder d'estimation (`livewire/estimation-builder.blade.php`)
- Bouton "Sauvegarder comme gabarit" ajouté dans le bandeau supérieur
- Ouvre une modale Alpine pour saisir le nom du gabarit
- Soumet un POST vers `estimations.save-as-template`

### Navigation (`components/layout.blade.php`)
- Lien "Gabarits" ajouté dans la nav principale, entre "Estimations" et "Paramètres"

### Vues créées
- `resources/views/templates/index.blade.php` — liste avec cartes (modifier, créer estimation, dupliquer, supprimer)
- `resources/views/templates/create.blade.php` — formulaire de création (miroir de estimations/create sans champs client)

---

## Étape 6 — Tests [x]

Fichier : `tests/Feature/TemplateTest.php` — 16 tests, 55 assertions, tous verts.

- [x] Affichage de la liste des gabarits
- [x] Création d'un gabarit depuis zéro (avec header/footer auto)
- [x] Validation du nom requis
- [x] Accès au builder
- [x] Isolation par utilisateur (builder — 403 si autre user)
- [x] Suppression (happy path + 403 si autre user)
- [x] Duplication d'un gabarit (pages + blocs copiés)
- [x] Création d'une estimation depuis un gabarit (pages, blocs, prix copiés)
- [x] Copie des add-ons gabarit → estimation
- [x] Sauvegarde d'une estimation comme gabarit (pages, blocs, prix copiés)
- [x] Validation nom requis pour saveAsTemplate
- [x] 403 si autre user tente saveAsTemplate
- [x] Isolation gabarits par user (index)
- [x] Page estimations/create affiche les gabarits de l'utilisateur
- [x] Page estimations/create ne montre pas les gabarits des autres users

---

## Dev Tracking

**Status**: Done
**Started**: 2026-04-04
**Completed**: 2026-04-04
**Developer**: BMAD Developer Agent

## Implementation Notes

### [2026-04-04] - Analyse de l'état initial
Les migrations, modèles (Template, TemplatePage, TemplatePageBlock), le composant Livewire TemplateBuilder, ses vues et le TemplateController étaient déjà créés par l'agent précédent. Il restait à faire : routes, saveAsTemplate, vues index/create, intégration UI et tests.

### [2026-04-04] - Bug : NOT NULL sur translation_fixed_hours
La colonne `translation_fixed_hours` dans `estimations` est NOT NULL en SQLite. Lors de la création d'une estimation depuis un gabarit dont ces champs sont null, une violation de contrainte survenait. Fix : utiliser `?? 0` sur les trois champs de traduction dans `TemplateController::createEstimation()` et `EstimationController::saveAsTemplate()`.

## Completion Summary

- **Implémenté** :
  - Routes templates + `estimations.save-as-template`
  - `EstimationController::saveAsTemplate()` avec copie pages/blocs/addons
  - `resources/views/templates/index.blade.php`
  - `resources/views/templates/create.blade.php`
  - Lien "Gabarits" dans la nav principale
  - Section "Partir d'un gabarit" dans `estimations/create`
  - Modal "Sauvegarder comme gabarit" dans le builder d'estimation
  - 16 tests PHPUnit couvrant tous les chemins

- **Fichiers modifiés** :
  - `routes/web.php`
  - `app/Http/Controllers/EstimationController.php`
  - `app/Http/Controllers/TemplateController.php`
  - `resources/views/components/layout.blade.php`
  - `resources/views/estimations/create.blade.php`
  - `resources/views/livewire/estimation-builder.blade.php`

- **Fichiers créés** :
  - `resources/views/templates/index.blade.php`
  - `resources/views/templates/create.blade.php`
  - `tests/Feature/TemplateTest.php`

- **Acceptance criteria** : ✅ Toutes les étapes du plan complétées
- **Suite complète** : 41 tests, 116 assertions, tous verts

---

## Avancement

| Étape | Statut |
|-------|--------|
| 1. Migrations + modèles | [x] Fait |
| 2. Livewire TemplateBuilder | [x] Fait |
| 3. Contrôleur + Routes | [x] Fait |
| 4. Intégration UI (création depuis gabarit) | [x] Fait |
| 5. Intégration UI (sauvegarder comme gabarit) | [x] Fait |
| 6. Tests | [x] Fait |
