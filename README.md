# Recruitment Task App - Symfony & Next.js

## 1. Opis projektu

Aplikacja do zarządzania zadaniami (To-Do List), z backendem w Symfony 7.2 i frontendem w Next.js.  
Wykorzystuje Docker do konteneryzacji oraz Minikube (Kubernetes) do zarządzania wdrożeniem.

## 2. Technologie

- Symfony 7.2 (PHP 8.3)
- Next.js (React)
- Mysql
- Docker + Docker Compose
- Kubernetes (Minikube)
- GitHub Actions (CI/CD)

## 3. Jak uruchomić projekt?

### a) Dodaj domene do pliku `/etc/hosts`:

```
cd etc/
sudo nano hosts
```

```
127.0.0.1	task-management.test
```

### b) Klonowanie repozytorium

```
git clone git@github.com:Jacqu82/task-management-app.git
```

### c) Uruchomienie w Dockerze

```
cd docker
docker compose build
docker compose up
```

### d) Instalacja Composer, migracji oraz JWT

#### Otwórz nowy terminal

```
cd docker
docker exec -it task-management-php bash
composer install
bin/console doctrine:migrations:migrate
bin/console lexik:jwt:generate-keypair
```

#### Testy PHPunit

```
vendor/bin/phpunit
```

### e) Instalacja npm oraz uruchomienie aplikacji

#### Otwórz nowy terminal

```
cd docker
docker exec -it task-management-node bash
npm install
npm run dev
```

#### Testy playwright
#### Otwórz nowy terminal

```
cd docker
docker exec -it task-management-node bash
npx playwright install
npx playwright install-deps
npm run test:e2e
```

### f) Otwórz w przeglądarce

```
http://task-management.test:8003/
```

## 📂 4. Struktura katalogów

- /.github - Github actions
- /backend` - Symfony API
- /docker - Pliki konfiguracji docker-a
- /frontend` - Next.js (React)
- /k8s` - Pliki konfiguracji Kubernetes


## ℹ️ 5. Dodatkowe informacje

🚀 W ramach tego projektu po raz pierwszy miałem realną okazję "pokodzić" w React.
Kubernetes poznałem po raz pierwszy i napotkałem na problemy z kontenerami Node'a i Composer'a, których
póki co nie potrafię rozwiązać. 

## 📩 6. Kontakt

```
jacqu25@yahoo.com
jacqu.wesoly82@gmail.com
```