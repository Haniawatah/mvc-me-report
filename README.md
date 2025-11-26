# MVC Report + Book Tracker Project

[![Build Status](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/build.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/build-status/main)
[![Coverage](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/code-structure/main/code-coverage)
[![Quality](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/haniawatah/mvc-me-report/?branch=main)

Ersätt USER/REPO med riktig GitHub path och aktivera projektet på Scrutinizer.

## Innehåll
- report/: kursrapport
- proj/: separat webbplats (landningssida + about, egen navbar, egen stil)
- src/: projekt + exempelklass
- tests/: PHPUnit tester (täckning >90%)
- docs/: dokumentation
- tools/: PHAR verktyg
- build/: genererat (coverage, phpdoc, metrics)

## Blackjack
Här kan du spela det klassiska kortspelet Blackjack mot datorn!
Målet är enkelt: få en hand med totalvärde närmare 21 än banken, utan att gå över.

*   **Starta spelet:** Gå till "Blackjack" i menyn eller via `/proj/blackjack`.
*   **Satsa:** Välj hur många mynt du vill satsa innan givan.
*   **Spela:** Välj "Hit" för att ta ett nytt kort eller "Stand" för att stanna.
*   **Vinn:** Om du slår banken vinner du insatsen tillbaka gånger två!

Spelet är byggt med objektorienterad PHP och använder sessioner för att hålla reda på spelets tillstånd. Klasserna ligger under `src/Blackjack`.

## Varför
Samlad kod för kursens rapport och projekt med kvalitetssäkring.

## Klona & starta
```bash
git clone https://github.com/haniawatah/mvc-me-report.git
cd mvc-me-report
php -S localhost:8080 -t public
# Besök http://localhost:8080/proj
```

## Test & coverage
```bash
./tools/install-tools.sh
php tools/phpunit.phar --configuration phpunit.xml.dist
```

## phpdoc
```bash
php tools/phpDocumentor.phar --config=phpdoc.xml.dist
```

## Metrics
```bash
php tools/phpmetrics.phar --config=phpmetrics.json
```

## Redovisning av krav

### Krav 1-3: Grundläggande spel och webbplats
Jag började med att bygga upp grunden för webbplatsen med landningssida och "Om"-sida. För själva spelet Blackjack skapade jag klasser för kort, kortlek och hand som jag återanvände från tidigare kursmoment men förbättrade strukturen på. Jag lade till en `Game`-klass som håller koll på spelets tillstånd, vems tur det är och vem som vunnit. Det var lite klurigt att få till logiken för ess (1 eller 11) men jag löste det genom en metod som räknar om värdet dynamiskt. Jag använde sessioner för att spara spelet mellan sidladdningar så att man inte tappar bort sina kort. Designen gjorde jag enkel och tydlig så att man lätt ser vad som händer.

### Krav 4: Smarta motståndare / Banken
För att göra banken lite smartare än att bara dra kort slumpmässigt implementerade jag en enkel AI-logik. Banken stannar alltid på 17 eller högre, vilket är standardregler för Blackjack. Jag lade in denna logik i `Game`-klassen så att när spelaren klickar på "Stanna", tar banken över och spelar sin hand automatiskt tills den antingen vinner, blir tjock eller stannar. Det krävdes lite testande för att se till att banken inte fuskade eller gjorde konstiga drag. Nu känns det som att man spelar mot en riktig dealer.

### Krav 5: Satsningar och spelkonto
Jag lade till funktionalitet för att kunna satsa pengar (låtsaspengar) i spelet. Jag skapade en enkel hantering i sessionen som håller koll på spelarens saldo. Innan varje runda får man välja hur mycket man vill satsa via ett formulär. Om man vinner får man dubbla insatsen tillbaka, och vid Blackjack får man 2.5 gånger pengarna. Om saldot tar slut har jag lagt till en knapp för att återställa pengarna så man kan fortsätta spela. Det var roligt att se hur spelet blev mer spännande när det fanns en insats med i bilden.

### Krav 6: Databas och historik
För att spara historik över spelade omgångar kopplade jag in en SQLite-databas via Doctrine ORM. Jag skapade en entitet `GameResult` som sparar vem som vann, datum och hur mycket som satsades. Efter varje avslutad runda sparas resultatet ner i databasen. Jag skapade sedan en separat sida där man kan se en tabell över de senaste 10 spelen. Det var lite struligt i början med att få Doctrine att fungera korrekt med migrationer, men efter att ha läst dokumentationen löste det sig. Det ger en bra överblick över hur det har gått i spelet.

## Om projektet
Projektet var både roligt och utmanande. Det svåraste var nog att få ihop alla delar med MVC-strukturen och se till att koden inte blev för rörig i kontrollerna. Jag försökte flytta så mycket logik som möjligt till klasserna i `src/Blackjack` för att hålla kontrollerna "smala". Det tog lite längre tid än jag trodde att fixa alla enhetstester och få upp kodtäckningen, men det var lärorikt. Ibland fastnade jag på småfel i Twig eller sessionhanteringen, men oftast gick det att lösa genom att felsöka steg för steg. Överlag tycker jag det var ett rimligt projekt för kursen då det knyter ihop allt vi lärt oss.

## Om kursen
Jag tycker kursen har varit väldigt lärorik och bra upplagd. Materialet har varit tydligt och övningarna har hjälpt mig att förstå hur moderna PHP-ramverk fungerar. Handledningen har varit bra, och jag har fått snabba svar när jag kört fast. Det enda jag kan tycka var lite tungt var mängden konfiguration i början med alla verktyg (linters, tester, metrics), men nu i efterhand ser jag nyttan med det. Jag skulle absolut rekommendera kursen till andra som vill lära sig mer om objektorienterad PHP och MVC. På en skala 1-10 ger jag kursen en stark 8.