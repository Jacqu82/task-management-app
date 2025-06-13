## Rezultat

Zadanie jeśli chodzi o sam kod, jest wykonywane akceptowalnie, jeśli chodzi o nasze wymagania na stanowisko Medium Full Stack PHP Developer. Są braki w zakresie środowiska uruchomieniowego, które sugerują, że praca z Dockerem być może nie była jeszcze wykonywana w środowisku uruchomieniowym produkcyjnym, i to na pewno by trzeba uzupełnić. Szkoda również, że nie udało się zdeployować manifestów Kubernetes, ponieważ to również jest ważna umiejętność na tym stanowisku. Istniejące w internecie materiały w mojej ocenie są wystarczające, aby umieć to zrobić nawet jeśli pierwsze zetknięcie nastąpiło przy okazji zadania, i warto tę część wiedzy poszerzyć, aby być konkurencyjnym w dzisiejszych czasach.

Poniżej wyniki analizy.

## Uruchomienie projektu

Pomimo informacji w README, nie udało się uruchomić testów jednostkowych:

```bash

root@5ea7afb94513:/web/backend# bin/phpunit

Fatal error: Trait "Symfony\Bridge\PhpUnit\Legacy\PolyfillAssertTrait" not found in /web/backend/vendor/bin/.phpunit/phpunit-9.6-0/src/Framework/Assert.php on line 93

```

W akcji GitHub CI widać, że testy przechodzą, więc problem dotyczy wyłącznie środowiska lokalnego. 

Testy Playwright udało się uruchomic i przeszły pomyślnie.

Aplikację udało się uruchomić i działa poprawnie. Przetestowano manualnie funkcjonalność.

Na plus akcja GH do CI, choć warto tutaj by było, żeby korzystała ona z obrazów Dockera, które służą do uruchamiania aplikacji lokalnie. Dzięki temu mielibyśmy pewność spójności środowisk. Problem ten staje się widoczny choćby już przy testach PHPUnit, które w akcji CI przechodzą, a lokalnie nie działają. Obrazy Docker dają szansę stworzenia środowiska lokalnego, które następnie bardzo łatwo wdrożyć na środowisko produkcyjne, ale też wykorzystać do wszelkich działań QA, takich jak testy jednostkowe czy integracyjne.

Manifesty k8s również nie korzystają ze stworzonych obrazów - powoduje to j/w, że środowisko lokalne nie jest spójne z ewentualnym produkcyjnym.

Same manifesty wyglądają raczej prawidłowo, ale nie została dostarczona instrukcja ich uruchomienia w README, nie podjęto więc próby uruchamiania. 

## Uwagi / sugestie

- Kroki w readme były nadmiarowe (docker compose up wystarczy zamiast build, jeśli obrazów nie ma, zbudują się automatycznie).

- Instalacja zależności composerem znajduje się już w obrazie `composer`, więc nie trzeba jej robić ręcznie. Uruchomienie obrazu `composer` przy brakujących zależnościach powoduje ich automatyczną instalację:

    ```bash
    task-management-composer  | Installing dependencies from lock file (including require-dev)
    task-management-composer  | Verifying lock file contents can be installed on current platform.
    task-management-composer  | Package operations: 90 installs, 0 updates, 0 removals
    task-management-composer  |   - Downloading symfony/flex (v2.5.0)
    task-management-composer  |   - Downloading symfony/runtime (v7.2.3)
    task-management-composer  |   - Downloading doctrine/cache (2.2.0)
    (...)
    ```

    Sugeruje to, że README zostało stworzone po zakończeniu prac, ale nie zweryfikowane na czysto - jest to dobra praktyka przy tworzeniu tego typu dokumentacji, w innym wypadku realne problemy przy instalacji pojawiają się dopiero przy próbie uruchomienia projektu przez inną osobę.

- (Sugestia) Można uruchamiać docker compose up z paramentem `-d`, aby działał w tle, a nie blokował terminala. Logi zawsze można sprawdzić poleceniem `docker compose logs`.

- (Dobra praktyka) Kroki takie jak `npm install`, migracje doctrine itd. tak naprawdę powinny się zawrzeć już w konfiguracji uruchomieniowej Docker; inicjalne postawienie projektu powinno się w miarę możliwości sprowadzać do uruchomienia `docker compose up -d` i przy dobrze wykonanej konfiguracji powinno to wystarczyć do uruchomienia projektu. Dzięki temu zdecydowanie ułatwiamy start nowym osobom z projektem - dzięki Dockerowi możemy zapewnić powtarzalne środowisko, które do rozpoczęcia pracy wymaga jedynie uruchomienia kontenerów.

- `npm run dev` powinno być zawarte w definicji kontenera `node`, aby nie trzeba było tego robić ręcznie. Nie powinno być konieczne przy każdym uruchamianiu projektu wchodzenie w terminal kontenera i uruchamianie tego polecenia. 

- Ta sama uwaga, jak wyżej, dotyczy testów Playwright. Można je wydzielić do osobnego kontenera, ale dobrze by było, gdyby kroki konieczne do instalacji nie były konieczne do wykonania ręcznie.

- [UI] Po rejestracji jeśli nie jest wymagana dodatkowa weryfikacja, warto byłoby przekierować użytkownika do strony logowania, zamiast pozostawiać go na stronie rejestracji. Albo też od razu go zalogować, jeśli rejestracja się powiodła.

- [UI] Zmiana statusu zadania warto, aby była możliwa bez konieczności otwierania zadania. Wystarczyłoby kliknąć na status i zmienić go bezpośrednio z listy zadań.

- Zostało opisane, że napotkane zostały problemy z kontenerami Node i Composer - warto byłoby wspomnieć, jakie to problemy i jakie kroki zostały podjęte, aby je rozwiązać. Czy np. w ogóle udało się wdrożyć stworzone manifesty, czy problem polegał już na uruchomieniu minikube itd.

- Walidacja wygląda w porządku, jednak w niektórych przypadkach jest niekompletna (np. długość `description` nie jest sprawdzana - oczywiście ustawiona długość jest bardzo duża, ale jednak warto byłoby to sprawdzić).

- [UI/UX] Brakuje HTML title, przez co w przeglądarce jako nazwa strony wyświetla się jej adres URL. Warto dodać tytuł, aby poprawić UX.

- [UI/UX] Format daty jest nieoczekiwany - w polskim środowisku spodziewalibyśmy się formatu DD.MM.YYYY HH:MM. Obecna data zadania nie posortuje się dobrze i trudno się ją czyta.

- [Testy] Testy Playwright mogłyby weryfikować prawdziwe działanie backendu, obecnie są oparte o mocki, co nie jest najlepszą praktyką. Warto byłoby przetestować prawdziwe API, aby mieć pewność, że działa ono poprawnie. Rzeczywiście wiemy, że frontend działa poprawnie, ale nie mamy pewności, że backend również. Oczywiście i tak jest to lepsze niż brak testów, ale warto byłoby to poprawić. Testy w oderwaniu od backendu mogą być przydatne, ale nie powinny być jedynymi testami e2e - dobrze, żeby testowały całą aplikację w jej pełnej funkcjonalności i środowisku uruchomieniowym. Np. pełen proces - rejestracja, logowanie, tworzenie zadania, zmiana statusu, edycja zadania, usunięcie zadania. Przy złożonych pełnoskalowych aplikacjach bardzo trudno jest zamockować całą aplikację oraz utrzymać spójność mocków z realnym backendem. W zakresie zadania jest to akceptowalne.
