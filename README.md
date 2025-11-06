# MVC me/report

Hej! Det här är mitt kursrepo för MVC-kursen. Jag försöker hålla koden smått pragmatisk:
liten refaktorering ofta, tester först när det känns rimligt, och verktyg som hjälper mig
se vart tiden gör mest nytta.

<p>
  <a href="https://scrutinizer-ci.com/g/Haniawatah/mvc-me-report/?branch=main" target="_blank" rel="noopener">
    <img src="https://img.shields.io/scrutinizer/build/g/Haniawatah/mvc-me-report/main?label=Build" alt="Build Status">
  </a>
  <a href="https://scrutinizer-ci.com/g/Haniawatah/mvc-me-report/?branch=main" target="_blank" rel="noopener">
    <img src="https://img.shields.io/scrutinizer/coverage/g/Haniawatah/mvc-me-report/main?label=Coverage" alt="Coverage">
  </a>
  <a href="https://scrutinizer-ci.com/g/Haniawatah/mvc-me-report/?branch=main" target="_blank" rel="noopener">
    <img src="https://img.shields.io/scrutinizer/quality/g/Haniawatah/mvc-me-report/main?label=Quality" alt="Quality">
  </a>
</p>

An annoying thing might happen -_-
If clicking a badge gives 404:
- Add the repo in Scrutinizer and enable the correct branch (main/master).
- Keep .scrutinizer.yml in the repo; push and trigger an analysis.
- Ensure coverage is uploaded (either run phpunit in Scrutinizer build or use external_code_coverage). 

Snabbstart
- Installera: composer install
- Dev-server (studentserver kör via public/): besök public/index.php enligt din BASE_URL
- Tester: composer phpunit
- Fix kodstil: composer csfix
- Metrics (phpmetrics): composer metrics
- PHPDoc (om verktyget finns lokalt): composer phpdoc

Var finns vad?
- /src: kod (controllers, entities, game, card)
- /templates: Twig-views
- /public: webroot för studentservern
- /docs/phpmetrics: lokalt genererade metrics-rapporter (kör “composer metrics”)

Varför alla små förändringar?
- Mindre magi, mer läsbarhet. Jag har t.ex. minskat antalet onödiga anrop i spelets dealer-loop,
  rensat bort oanvänd kod och gjort JSON/HTML‑representationerna för korten konsekventa.
- Jag föredrar små förbättringar som syns i verktygen (och i koden) framför stora omskrivningar.