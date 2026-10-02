# SQVALD — consortium website

[Français](README.fr.md) · **English**

[![Tests](https://github.com/lokiap/SQVALD/actions/workflows/tests.yml/badge.svg)](https://github.com/lokiap/SQVALD/actions/workflows/tests.yml)
![Symfony](https://img.shields.io/badge/Symfony-5.3-000000?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.1%20%7C%208.2-777BB4?logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-13-4169E1?logo=postgresql&logoColor=white)
![EasyAdmin](https://img.shields.io/badge/EasyAdmin-3-5a67d8)
![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)

Web platform for **SQVALD**, a regional research project (Centre-Val de Loire, RTR DIAMS programme) on supportive care for patients with chronic and long-term illnesses such as cancer. The project brings together a computer science lab, hospitals, patient associations and local partners. This website is their shared space: it presents the project to the public and lets consortium members publish news, events, documents and videos, with an admin team validating every account and every piece of content.

![Home page](docs/screenshots/home.png)

## Features

**Public site**
- Home page with a rolling banner of the latest news and a partner carousel.
- "About" page with the project's objectives and work packages.
- News, events, documents and videos, each with a search/filter panel and pagination.
- Calendar of events, browsable by year.
- Consortium page listing the partner organisations and their members.

**Members' area**
- Sign-up with e-mail verification, then manual validation by an admin before the first login.
- Personal space to edit one's profile and manage one's own documents, events, news and videos.
- Rich-text editing (CKEditor) and file uploads (images, PDF, Word) with generated thumbnails.
- Password reset by e-mail.

**Administration**
- EasyAdmin dashboard with counters and a validation queue (pending accounts, documents, events, news, videos).
- Full CRUD on every entity, partners and users included.
- Automatic e-mails: admins are notified when new content is posted, and users when their account is validated.

## Screenshots

> Taken on a local instance loaded with the demo fixtures (see below).

| News | Calendar |
|---|---|
| ![News](docs/screenshots/news.png) | ![Calendar](docs/screenshots/calendar.png) |
| **Documents** | **Partners** |
| ![Documents](docs/screenshots/documents.png) | ![Partners](docs/screenshots/partners.png) |
| **Admin dashboard** | **Member space** |
| ![Admin dashboard](docs/screenshots/admin.png) | ![Member space](docs/screenshots/account.png) |

## Architecture

```mermaid
flowchart LR
    V[Visitor] --> P
    M[Member] --> P
    A[Admin] --> AD

    subgraph Symfony["Symfony 5.3 app"]
        P["Public & member pages<br/>Controllers + Twig"]
        AD["EasyAdmin<br/>dashboard & CRUD"]
        SEC["Security<br/>login, e-mail verification,<br/>admin validation"]
        SUB["Event subscribers<br/>notification e-mails"]
        UP["VichUploader + LiipImagine<br/>files & thumbnails"]
    end

    P --> SEC
    AD --> SEC
    P --> UP
    P & AD --> ORM[(Doctrine ORM)]
    ORM --> DB[(PostgreSQL)]
    ORM -. postPersist / preUpdate .-> SUB
    SUB --> MAIL[Symfony Mailer]
    UP --> FS[public/uploads]
```

### Account lifecycle

A new member can only log in once they have confirmed their e-mail **and** an admin has validated the account. Both checks run in `CheckVerifiedUserSubscriber` at login time.

```mermaid
sequenceDiagram
    actor U as New member
    participant S as SQVALD
    actor A as Admin
    U->>S: Sign up
    S-->>U: Verification e-mail
    U->>S: Clicks the link (account verified)
    S-->>A: Account appears in the validation queue
    A->>S: Validates the account
    S-->>U: "Account validated" e-mail
    U->>S: Logs in and publishes content
    S-->>A: New-content notification e-mail
```

### Data model

```mermaid
erDiagram
    PARTNER ||--o{ USER : "employs"
    USER }o--o{ NEWS : "writes"
    USER }o--o{ EVENT : "organises"
    USER }o--o{ DOCUMENT : "authors"
    USER }o--o{ VIDEO : "posts"
    CATEGORY_NEWS ||--o{ EVENT : "classifies"
    CATEGORY_DONNEES ||--o{ DOCUMENT : "classifies"
    USER ||--o{ RESET_PASSWORD_REQUEST : "requests"

    USER {
        string email
        json roles
        bool isVerified
        bool isValide
    }
    NEWS {
        string title
        text content
        bool isActive
    }
    EVENT {
        string title
        date dateBegin
        date dateEnd
        string place
        bool isActive
    }
    DOCUMENT {
        string title
        string picture
        string brochureFilename
        bool isActive
    }
    VIDEO {
        string title
        string link
        bool isActive
    }
```

Every piece of content has an `isActive` flag: it stays hidden from the public site until an admin publishes it.

## Tech stack

| Layer | Tools |
|---|---|
| Framework | Symfony 5.3, Twig, Symfony Forms & Validator |
| Data | Doctrine ORM & Migrations, PostgreSQL 13 |
| Admin | EasyAdmin 3 |
| Auth | Symfony Security, SymfonyCasts Verify Email & Reset Password |
| Content | CKEditor, VichUploader, LiipImagine, KnpPaginator |
| E-mail | Symfony Mailer |
| Front-end | Bootstrap, Font Awesome |
| Quality | PHPUnit functional tests, GitHub Actions |
| Deployment | Docker Compose (database), Heroku `Procfile` |

## Getting started

Requirements: PHP 8.1 or 8.2 with the `intl`, `pdo_pgsql` and `gd` extensions, Composer, and Docker (or a local PostgreSQL 13).

```bash
git clone https://github.com/lokiap/SQVALD.git
cd SQVALD
composer install

# PostgreSQL on port 5432 + pgAdmin on http://localhost:5050
docker compose up -d

php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load     # demo data
symfony serve                              # or: php -S 127.0.0.1:8000 -t public
```

The committed `.env` only holds local defaults that match `docker-compose.yml`. Real settings (database, `APP_SECRET`, `MAILER_DSN`) go in a `.env.local` file, which git ignores. E-mails are discarded by default (`MAILER_DSN=null://null`).

### Demo accounts

The fixtures create the consortium partners, news, events and documents, plus these accounts (password `demo1234` for all of them):

| E-mail | Role |
|---|---|
| `admin@demo.local` | Admin |
| `b.durand@demo.local` | Member |
| `c.petit@demo.local` | Member |
| `d.moreau@demo.local`, `e.leroy@demo.local` | Waiting for admin validation (cannot log in yet) |

## Tests

Functional tests cover the public pages, the publication filter (`isActive`), the calendar, access control and the login rules (an account waiting for validation is refused). They run on GitHub Actions against PostgreSQL on every push.

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate -n --env=test
php bin/console doctrine:fixtures:load -n --env=test
php bin/phpunit
```

## Project structure

```
src/
├── Controller/        # public pages, member space, Admin/ (EasyAdmin CRUD)
├── DataFixtures/      # demo data
├── Entity/            # User, Partner, News, Event, Document, Video, categories
├── EventSubscriber/   # login checks and notification e-mails
├── Form/              # entity forms and search filters
├── Repository/        # Doctrine queries (search, calendar by year)
└── Security/          # authenticator, e-mail verifier
templates/             # Twig views, one folder per section
tests/                 # functional tests (PHPUnit)
migrations/            # Doctrine migrations
public/uploads/        # uploaded files (not versioned)
```

## License

[MIT](LICENSE)
