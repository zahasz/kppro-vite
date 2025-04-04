# KPPRO - System Zarządzania Firmą

KPPRO to nowoczesna aplikacja webowa do zarządzania firmą, zbudowana w oparciu o Laravel i Vue.js. System umożliwia zarządzanie subskrypcjami, fakturami, płatnościami i wieloma innymi aspektami prowadzenia działalności.

## Funkcje

- **System subskrypcji** - różne plany z określonymi limitami i dostępnymi funkcjami
- **Fakturowanie** - tworzenie i zarządzanie fakturami
- **Płatności online** - integracja z bramkami płatności
- **Panel administratora** - zarządzanie użytkownikami, subskrypcjami i płatnościami
- **Profil firmy** - pełne zarządzanie danymi firmy i kontami bankowymi
- **Wielojęzyczność** - wsparcie dla różnych języków
- **Responsywny design** - dostosowany do urządzeń mobilnych i desktopowych

## Wymagania

- PHP 8.1 lub nowszy
- Composer
- Node.js i npm/yarn
- MySQL 5.7 lub nowszy
- Serwer WWW (Apache, Nginx)

## Instalacja

1. Sklonuj repozytorium:
   ```
   git clone https://github.com/twoje-konto/kppro-vite.git
   cd kppro-vite
   ```

2. Zainstaluj zależności PHP:
   ```
   composer install
   ```

3. Zainstaluj zależności JavaScript:
   ```
   npm install
   ```

4. Skonfiguruj plik .env:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. Skonfiguruj połączenie z bazą danych w pliku .env.

6. Przeprowadź migracje i seedowanie danych:
   ```
   php artisan seed:startup --fresh
   ```

7. Skompiluj assets:
   ```
   npm run dev
   ```

8. Uruchom serwer:
   ```
   php artisan serve
   ```

## Inicjalizacja systemu

System zawiera specjalną komendę do inicjalizacji całego systemu:

```
php artisan seed:startup
```

Dostępne opcje:
- `--fresh` - Czyści bazę danych przed wykonaniem migracji i seedowania
- `--migrate` - Wykonuje tylko migracje, bez seedowania danych
- `--force` - Wymusza wykonanie migracji w środowisku produkcyjnym

Po wykonaniu komendy `seed:startup` zostanie utworzone konto administratora:
- **Email**: admin@kppro.pl
- **Hasło**: admin123

## Struktura systemu

- **app/** - Główny katalog aplikacji z kontrolerami, modelami i innymi klasami
- **database/migrations/** - Migracje bazy danych
- **database/seeders/** - Seedery do inicjalizacji danych
- **resources/views/** - Widoki Blade
- **resources/js/** - Komponenty Vue.js
- **routes/** - Definicje tras

## Moduły systemu

1. **Subskrypcje** - Zarządzanie planami subskrypcji i subskrypcjami użytkowników
2. **Płatności** - Integracja z bramkami płatności i przetwarzanie transakcji
3. **Fakturowanie** - Automatyczne generowanie faktur
4. **Panel administratora** - Zarządzanie systemem
5. **Panel użytkownika** - Zarządzanie kontem i subskrypcjami

## Wsparcie i rozwój

W razie problemów lub pytań, proszę zgłaszać je przez system Issues na GitHubie.

## Licencja

Aplikacja jest własnością KPPRO i jest objęta licencją własnościową. 