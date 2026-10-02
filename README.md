# SQVALD — consortium website

[Français](README.fr.md) · **English**

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

> Taken on a local instance filled with demo data.

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
    SUB --> MAIL[Mailer / Mailjet]
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
| E-mail | Symfony Mailer, Mailjet |
| Front-end | Bootstrap, Font Awesome |
| Deployment | Docker Compose (database), Heroku `Procfile` |

## Getting started

Requirements: PHP 8.1 or 8.2 with the `intl`, `pdo_pgsql` and `gd` extensions, Composer, and Docker (or a local PostgreSQL 13).

```bash
git clone https://github.com/lokiap/SQVALD.git
cd SQVALD
composer install

# PostgreSQL + pgAdmin (http://localhost:5050)
docker compose up -d
```

Create a `.env.local` file with your own settings (never commit it):

```dotenv
APP_ENV=dev
APP_SECRET=change-me
DATABASE_URL="postgresql://postgres:admin@127.0.0.1:<port>/sqvald?serverVersion=13&charset=utf8"
MAILER_DSN=null://null
```

`<port>` is the host port Docker gave PostgreSQL (`docker compose port database 5432`).

Then create the schema and start the server:

```bash
php bin/console doctrine:schema:create
php bin/console assets:install public
symfony serve        # or: php -S 127.0.0.1:8000 -t public
```

To get an admin account, sign up through the site, then in the database set `is_verified` and `is_valide` to `true` and `roles` to `["ROLE_ADMIN"]` for that user. Document and event categories (Article, Report, Seminar…) are then created from the admin dashboard.

## Project structure

```
src/
├── Controller/        # public pages, member space, Admin/ (EasyAdmin CRUD)
├── Entity/            # User, Partner, News, Event, Document, Video, categories
├── EventSubscriber/   # login checks and notification e-mails
├── Form/              # entity forms and search filters
├── Repository/        # Doctrine queries (search, calendar by year)
└── Security/          # authenticator, e-mail verifier
templates/             # Twig views, one folder per section
migrations/            # Doctrine migrations
public/uploads/        # uploaded files
```

## License

[MIT](LICENSE)
