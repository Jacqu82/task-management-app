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

### a) Klonowanie repozytorium

```
git clone git@github.com:Jacqu82/task-management-app.git
```

### b) Uruchomienie w Dockerze

```
cd docker
docker-compose build
docker-compose up
```

### c) Instalacja Composer, migracji oraz JWT

Otwórz nowy terminal

```
cd docker
docker exec -it task-management-php-fpm bash
composer install
bin/console doctrine:migrations:migrate
bin/console lexik:jwt:generate-keypair
```

Testy PHPunit

```
bin/phpunit
```

### d) Instalacja npm oraz uruchomienie aplikacji

Otwórz nowy terminal

```
cd docker
docker exec -it task-management-node bash
npm install
npm run dev
```

Testy playwright
Otwórz nowy terminal

```
cd docker
docker exec -it task-management-node bash
npm run playwright test
```

### e) Otwórz w przeglądarce

```
http://task-management.test:8000/
```

## 📂 4. Struktura katalogów

- /.github - Github actions
- /backend` - Symfony API
- /docker - Pliki konfiguracji dockera
- /frontend` - Next.js (React)
- /k8s` - Pliki konfiguracji Kubernetes


## ℹ️ 5. Dodatkowe informacje

🚀 W ramach tego projektu po raz pierwszy miałem styczność z React i Kubernetes, co było świetnym doświadczeniem.   
Niektóre rozwiązania mogłyby być jeszcze zoptymalizowane, ale projekt spełnia wymagania i działa poprawnie.  

## 📩 6. Kontakt

```
jacqu25@yahoo.com
jacqu.wesoly82@gmail.com
```