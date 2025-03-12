# Zadanie rekrutacyjne: aplikacja do zarządzania zadaniami

## Opis zadania

Stwórz aplikację, która umożliwia zarządzanie zadaniami (to-do list).

## Wymagania biznesowe

1. **Zarządzanie zadaniami**:
    - **Tworzenie nowych zadań**: Użytkownik powinien móc tworzyć zadania, podając tytuł i szczegóły.
    - **Edycja zadań**: Użytkownik może edytować tytuł, opis oraz status zadania.
    - **Usuwanie zadań**: Użytkownik może usuwać zadania.
    - **Statusy zadań**: Zadanie powinno mieć jeden z trzech statusów: `Oczekujące`, `W trakcie`, `Zakończone`.
2. **(opcjonalnie) Interfejs API (RESTful)**:
    - Udostępnij interfejs RESTful do zarządzania zadaniami oraz użytkownikami (z użyciem JSON).

Aplikacja powinna wymagać zalogowania. Sposób autoryzacji do wyboru.

## Wymagania funkcjonalne

- Aplikacja powinna być dostępna przez przeglądarkę w wersji mobilnej (RWD). Powinna prawidłowo działać w trybie emulacji urządzenia mobilnego (smartfon).

## Wymagania techniczne

- **Backend:**
  - Symfony (PHP)
  - Doctrine ORM do komunikacji z bazą danych (MariaDB)
- **Frontend:**
  - Next.js (React)
- **Baza danych:**
  - MariaDB
- **Docker**:
  - Konteneryzacja aplikacji za pomocą Docker z wykorzystaniem Docker Compose (kontener dla backendu, frontend oraz baza danych).
- **Kubernetes (Minikube):**
  - Przygotowanie aplikacji do uruchomienia na Minikube z manifestami YAML do konfiguracji środowiska
- **Kilka przykładowych testów:**
  - Testy akceptacyjne dla frontendu, umożliwiające weryfikację jego prawidłowego działania.

Istotne jest zadbanie o bezpieczeństwo aplikacji (np. walidacja danych, zabezpieczenie przed atakami CSRF, SQL Injection).

Repozytorium aplikacji powinno być kompletne (zawierać wszystkie niezbędne pliki, które pozwolą od zera uruchomić kompletny projekt, działający w przeglądarce). Należy podać w dokumentacji projektu listę koniecznych kroków np. komend do uruchomienia czy plików konfiguracyjnych, koniecznych do utworzenia.

## Sposób dostarczenia efektów prac

- Nadanie dostępu do repozytorium prywatnego z efektem prac na maila `wadamczyk@insolutions.pl` w GitHub.
- W repozytorium obowiązkowy plik `README.md` z opisem projektu i sposobu jego uruchomienia krok po kroku - od pobrania repozytorium do uruchomienia w przeglądarce.
