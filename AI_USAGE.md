# AI_USAGE

## Tools & model
- Claude (Anthropic), melalui antarmuka chat claude.ai.
- Google ai

## Bagian yang dikerjakan dengan bantuan AI
Pembuatan awal (draft) seluruh repository: skema/migration, model, service, middleware idempotency, state machine, job, controller, test PHPUnit & Vitest, Nuxt frontend, Dockerfile, docker-compose, GitHub Actions, README, ARCHITECTURE.

## Prompt utama (disalin apa adanya)
1. > buatkan project sesuai dengan ketentuan pada file pdf ini dengan ketentuan pilihan untuk backend saya pilih track B dan untuk frontendnya saya menggunakan Vue.js  + Nuxt dengan ketentuan sesuai dengan soal dengan lengkap
2. > `Warning: PHP Startup: Unable to load dynamic library 'redis' (tried: ext\redis (The specified module could not be found), ext\php_redis.dll (The specified module could not be found)) in Unknown on line 0
PHP Warning: PHP Startup: Unable to load dynamic library 'redis' (tried: ext\redis (The specified module could not be found), ext\php_redis.dll (The specified module could not be found)) in Unknown on line 0`
3. > `Illuminate\Database\QueryException

 SQLSTATE[42P07]: Duplicate table: 7 ERROR: relation "personal_access_tokens" already exists (Connection: pgsql, Host: localhost, Port: 5432, Database: azatech, SQL: create table "personal_access_tokens" ("id" bigserial not null primary key, "tokenable_type" varchar(255) not null, "tokenable_id" bigint not null, "name" text not null, "token" varchar(64) not null, "abilities" text null, "last_used_at" timestamp(0) without time zone null, "expires_at" timestamp(0) without time zone null, "created_at" timestamp(0) without time zone null, "updated_at" timestamp(0) without time zone null))

 at vendor\laravel\framework\src\Illuminate\Database\Connection.php:864
 860▕ $exceptionType = ($isUniqueConstraintError = $this->isUniqueConstraintError($e))
 861▕ ? UniqueConstraintViolationException::class
 862▕ : QueryException::class;
 863▕
 ➜ 864▕ $exception = new $exceptionType(
 865▕ $this->getNameWithReadWriteType(),
 866▕ $query,
 867▕ $this->prepareBindings($bindings),
 868▕ $e,

 1 vendor\laravel\framework\src\Illuminate\Database\Connection.php:608
 PDOException::("SQLSTATE[42P07]: Duplicate table: 7 ERROR: relation "personal_access_tokens" already exists")

 2 vendor\laravel\framework\src\Illuminate\Database\Connection.php:608
 PDOStatement::execute()`

4. > Error SQLSTATE[42P07]: Duplicate table... relation "personal_access_tokens" already exists terjadi karena Laravel mencoba membuat tabel personal_access_tokens saat Anda menjalankan php artisan migrate, tetapi tabel tersebut sudah ada di dalam database PostgreSQL (azatech) Anda.

## Bagian yang saya tulis / ubah sendiri
**disisi backend**
1. > saya merubah pada koneksi database, route, appProvider, mengubah sebagian controllernya, models, dan sebagian controller, menambahkan dockerfile

**disisi frontend**
1. > saya merubah tampilan dashboard, login, menambahkan dockerfile

**disisi root folder**
1. > saya mnambhakan konfigurasi di docker-compose

