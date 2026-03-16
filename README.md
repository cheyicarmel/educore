# EduCore — Système de Gestion Scolaire

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?style=for-the-badge&logo=laravel" />
  <img src="https://img.shields.io/badge/PHP-8.2-blue?style=for-the-badge&logo=php" />
  <img src="https://img.shields.io/badge/MySQL-8.0-orange?style=for-the-badge&logo=mysql" />
  <img src="https://img.shields.io/badge/TailwindCSS-3-38bdf8?style=for-the-badge&logo=tailwindcss" />
  <img src="https://img.shields.io/badge/Statut-Portfolio-green?style=for-the-badge" />
</p>

<p align="center">
  <a href="#french">🇫🇷 Français</a> &nbsp;|&nbsp;
  <a href="#english">🇬🇧 English</a>
</p>

---

<a name="french"></a>

## 🇫🇷 Français

### Présentation

**EduCore** est une application web complète de gestion scolaire développée avec Laravel 12. Elle couvre l'ensemble du cycle de vie scolaire : inscription des élèves, gestion des notes, calcul des moyennes, publication des bulletins, suivi financier et passage en classe supérieure.

---

### Fonctionnalités principales

#### 👤 Super Admin & Admin
- Gestion des années académiques, classes, matières, séries
- Inscription des élèves avec création automatique de compte
- Gestion des enseignants et attributions de matières par classe
- Publication des bulletins PDF par classe (S1, S2, annuel)
- Suivi financier global et par classe
- Passage en classe supérieure avec calcul automatique des séries
- Paramètres de l'établissement (nom, logo, slogan, seuils de mention)
- Recherche globale en temps réel (élèves, enseignants, classes, matières, séries)

#### 🧑‍🏫 Enseignant
- Saisie des notes par classe et par semestre (interrogations + devoirs)
- Validation et calcul des moyennes (par matière, semestrielles, annuelles)
- Classement des élèves de sa classe principale
- Génération de relevés de notes PDF (semestriel et annuel)

#### 🎓 Élève
- Consultation des notes par semestre avec calcul en temps réel
- Accès aux bulletins PDF (bloqué si frais impayés)
- Historique consultable par année académique
- Report automatique du solde impayé sur la nouvelle année

#### 💰 Comptable
- Enregistrement des paiements avec génération de reçu PDF
- Suivi financier par élève (total dû, payé, solde restant)
- Rapport financier global PDF par classe
- Historique complet des paiements

---

### Règles métier importantes

#### Calcul des moyennes
- **3 interrogations + 2 devoirs** par matière par semestre
- Moyenne interrogations = (I1 + I2 + I3) ÷ 3
- Moyenne générale matière = (Moy.Interro + D1 + D2) ÷ 3
- **6ème / 5ème** : Moyenne semestrielle = Somme des moyennes ÷ nombre de matières (sans coefficients)
- **Autres niveaux** : Moyenne semestrielle = Somme(moy × coef) ÷ Somme des coefficients
- Moyenne annuelle = (S2 × 2 + S1) ÷ 3

#### Décision de fin d'année
- Moyenne annuelle ≥ 10 → **Passant**
- Moyenne annuelle < 10 → **Doublant**

#### Attribution des séries (passage de classe)
- **5ème → 4ème / 4ème → 3ème** : Comparaison moyenne littéraire vs scientifique → L ou C
- **3ème L → 2nde** : Automatiquement série A
- **3ème C → 2nde** : Analyse par sous-groupes (Maths+Physique / SVT / Littéraire) → C, D ou A
- **2nde / 1ère** : Même logique second cycle
- L'administrateur peut corriger manuellement la série après le passage

#### Frais scolaires
- Les frais sont définis par classe (non par élève)
- En cas de solde impayé, le montant restant est reporté sur la nouvelle année
- L'accès aux bulletins est bloqué tant que le solde n'est pas soldé

---

### Stack technique

| Technologie | Version | Usage |
|---|---|---|
| Laravel | 12 | Framework backend |
| PHP | 8.2 | Langage serveur |
| MySQL | 8.0 | Base de données |
| Tailwind CSS | 3 (CDN) | Styles |
| Alpine.js | 3 (CDN) | Interactions JS légères |
| DomPDF | - | Génération de PDF |
| Mailtrap | - | Envoi d'emails en developpement (uniquement pour les tests) |
| Chart.js | - | Graphiques dashboard |

---

### Structure de la base de données

La base comporte **21 tables principales** :

```
users                   → Comptes utilisateurs (tous acteurs)
eleves                  → Profils élèves
enseignants             → Profils enseignants
comptables              → Profils comptables
annees_academiques      → Années scolaires
classes                 → Classes par année
series                  → Séries (A, C, D, L...)
matieres                → Matières enseignées
attributions            → Matière × Classe × Enseignant × Année
inscriptions            → Élève × Classe × Année
notes                   → Notes par élève, matière, semestre
moyenne_matieres        → Moyennes calculées par matière
moyenne_semestres       → Moyennes semestrielles
moyenne_annuelles       → Moyennes annuelles + décision
bulletins               → Bulletins PDF générés
suivi_financiers        → Suivi paiements par inscription
paiements               → Historique des paiements
parametres              → Paramètres de l'établissement
```

---

### Installation locale

#### Prérequis
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (optionnel)

#### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/cheyicarmel/educore.git
cd educore

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Lancer les migrations et seeders
php artisan migrate
php artisan db:seed

# 5. Créer le lien symbolique pour le stockage
php artisan storage:link

# 6. Lancer le serveur
php artisan serve
```

---

### Comptes de démonstration

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Admin | admin@educore.com | Admin@1234 |
| Admin | shw.educore@gmail.com | cGXgifOcGc |
| Enseignant | k.educore@gmail.com | AJF3Go25M2 |
| Élève | marthe.agbeko708@parent-test.com | password123 |
| Comptable | matt.c@educore.com | SSw0OSqzgR |

---

### Routes principales

#### Admin
| Méthode | Route | Description |
|---|---|---|
| GET | /admin/dashboard | Tableau de bord |
| GET | /admin/eleves | Liste des élèves |
| GET | /admin/enseignants | Liste des enseignants |
| GET | /admin/classes | Gestion des classes |
| GET | /admin/matieres | Gestion des matières |
| GET | /admin/bulletins | Publication des bulletins |
| GET | /admin/finances | Suivi financier |
| GET | /admin/passage | Passage en classe supérieure |
| GET | /admin/parametres | Paramètres établissement |

#### Enseignant
| Méthode | Route | Description |
|---|---|---|
| GET | /enseignant/dashboard | Tableau de bord |
| GET | /enseignant/notes | Saisie des notes |
| GET | /enseignant/classes | Mes classes |
| GET | /enseignant/ma-classe | Ma classe principale |

#### Élève
| Méthode | Route | Description |
|---|---|---|
| GET | /eleve/dashboard | Tableau de bord |
| GET | /eleve/notes | Mes notes |
| GET | /eleve/bulletins | Mes bulletins |

#### Comptable
| Méthode | Route | Description |
|---|---|---|
| GET | /comptable/dashboard | Tableau de bord |
| GET | /comptable/paiements/creer | Enregistrer un paiement |
| GET | /comptable/historique | Historique des paiements |
| GET | /comptable/documents | Documents financiers |


---

---

<a name="english"></a>

## 🇬🇧 English

### Overview

**EduCore** is a full-featured school management web application built with Laravel 12. It covers the complete school lifecycle: student enrollment, grade management, average calculation, report card publication, financial tracking, and class promotion.

---

### Key Features

#### 👤 Super Admin & Admin
- Manage academic years, classes, subjects, and series
- Enroll students with automatic account creation
- Manage teachers and subject assignments per class
- Publish PDF report cards per class (Semester 1, 2, annual)
- Global and per-class financial tracking
- Automatic class promotion with series calculation
- School settings (name, logo, slogan, grade thresholds)
- Real-time global search (students, teachers, classes, subjects, series)

#### 🧑‍🏫 Teacher
- Enter grades per class and semester (quizzes + assignments)
- Validate and calculate averages (per subject, per semester, annual)
- Rank students in their main class
- Generate PDF grade reports (semester and annual)

#### 🎓 Student
- View grades per semester with real-time calculation
- Access PDF report cards (blocked if fees are unpaid)
- Browse history by academic year
- Unpaid balance automatically carried over to the new year

#### 💰 Accountant
- Record payments and generate PDF receipts
- Track finances per student (total due, paid, remaining balance)
- Generate global PDF financial report by class
- Full payment history

---

### Business Rules

#### Grade Calculation
- **3 quizzes + 2 assignments** per subject per semester
- Quiz average = (Q1 + Q2 + Q3) ÷ 3
- Subject average = (Quiz avg + A1 + A2) ÷ 3
- **6th / 5th grade**: Semester average = Sum of averages ÷ number of subjects (no coefficients)
- **Other levels**: Semester average = Sum(avg × coef) ÷ Sum of coefficients
- Annual average = (S2 × 2 + S1) ÷ 3

#### Year-end Decision
- Annual average ≥ 10 → **Promoted**
- Annual average < 10 → **Repeating**

#### Series Assignment (Class Promotion)
- **5th → 4th / 4th → 3rd**: Compare literary vs scientific average → L or C
- **3rd L → 2nd**: Automatically series A
- **3rd C → 2nd**: Sub-group analysis (Math+Physics / Biology / Literary) → C, D or A
- **2nd / 1st**: Same second cycle logic
- Admin can manually override the assigned series after promotion

#### School Fees
- Fees are defined per class (not per student)
- Unpaid balances are carried over to the new academic year
- Report card access is blocked until balance is fully paid

---

### Tech Stack

| Technology | Version | Usage |
|---|---|---|
| Laravel | 12 | Backend framework |
| PHP | 8.2 | Server language |
| MySQL | 8.0 | Database |
| Tailwind CSS | 3 (CDN) | Styling |
| Alpine.js | 3 (CDN) | Lightweight JS interactions |
| DomPDF | - | PDF generation |
| Mailtrap | - | Email sending in development (just for tests) |
| Chart.js | - | Dashboard charts |

---

### Local Installation

#### Requirements
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (optional)

#### Steps

```bash
# 1. Clone the project
git clone https://github.com/cheyicarmel/educore.git
cd educore

# 2. Install dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seeders
php artisan migrate
php artisan db:seed

# 5. Create storage symlink
php artisan storage:link

# 6. Start the server
php artisan serve
```

---

### Demo Accounts

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@educore.com | Admin@1234 |
| Admin | shw.educore@gmail.com | cGXgifOcGc |
| Teacher | k.educore@gmail.com | AJF3Go25M2 |
| Student | marthe.agbeko708@parent-test.com | password123 |
| Accountant | matt.c@educore.com | SSw0OSqzgR |

---

### Main Routes

#### Admin
| Method | Route | Description |
|---|---|---|
| GET | /admin/dashboard | Dashboard |
| GET | /admin/eleves | Student list |
| GET | /admin/enseignants | Teacher list |
| GET | /admin/classes | Class management |
| GET | /admin/matieres | Subject management |
| GET | /admin/bulletins | Report card publication |
| GET | /admin/finances | Financial tracking |
| GET | /admin/passage | Class promotion |
| GET | /admin/parametres | School settings |

#### Teacher
| Method | Route | Description |
|---|---|---|
| GET | /enseignant/dashboard | Dashboard |
| GET | /enseignant/notes | Grade entry |
| GET | /enseignant/classes | My classes |
| GET | /enseignant/ma-classe | My main class |

#### Student
| Method | Route | Description |
|---|---|---|
| GET | /eleve/dashboard | Dashboard |
| GET | /eleve/notes | My grades |
| GET | /eleve/bulletins | My report cards |

#### Accountant
| Method | Route | Description |
|---|---|---|
| GET | /comptable/dashboard | Dashboard |
| GET | /comptable/paiements/creer | Record payment |
| GET | /comptable/historique | Payment history |
| GET | /comptable/documents | Financial documents |

---

<p align="center">
  Développé par Carmel ADISSA — EduCore © 2026
</p>