# Azatech Order Management

Monorepo technical-test implementation: Laravel 13 API (Track B) + Nuxt 3/Vue 3 TypeScript frontend.

## Stack
- Backend: PHP 8.3+, Laravel 13, Sanctum, PostgreSQL, Redis Queue/Cache, Pest, Scramble/OpenAPI.
- Frontend: Nuxt 3, Vue 3, TypeScript, Tailwind CSS, TanStack Vue Query, Vitest, Testing Library.
- Infra: Docker Compose, PostgreSQL, Redis, Mailpit, GitHub Actions.

## Quick start
1. `cp backend/.env.example backend/.env`
2. `cp frontend/.env.example frontend/.env`
3. `docker compose up --build -d`
4. `docker compose exec api php artisan key:generate`
5. `docker compose exec api php artisan migrate --seed`
6. Open web at http://localhost:3000, API at http://localhost:8000, Mailpit at http://localhost:8025, API docs at http://localhost:8000/docs/api.

Seed credentials: `admin@azatech.id / password` and `cashier@azatech.id / password`. Change these outside local development.

## Tests
- Backend: `docker compose exec api php artisan test`
- Static analysis: `docker compose exec api vendor/bin/phpstan analyse`
- Formatting: `docker compose exec api vendor/bin/pint --test`
- Frontend: `docker compose exec web npm run test`
- Frontend typecheck: `docker compose exec web npm run typecheck`

## Services
`api`, `worker`, `web`, `db`, `redis`, and `mailpit` run independently. Queue worker uses Redis. Migrations/seeding are explicit so deployments can control schema changes.

## AI Usage
See [AI_USAGE.md](AI_USAGE.md). AI-assisted code must be reviewed, understood, tested, and adapted by the candidate before submission.

## Known implementation notes
This repository is a complete working-oriented baseline. Run the full test suite in the target Docker environment and review framework-version compatibility before submission. Replace local secrets, configure production mail/storage, and complete any environment-specific deployment hardening. `DECISIONS.md` must be updated with bugs actually encountered and the candidate's own decisions before interview/submission.
