## Pulse Workshop

Repository per il workshop su Laravel Pulse organizzato dal Grusp a Verona il giorno 8 novembre 2024.

### Istruzioni per l'installazione

Per installare l'applicazione di esempio eseguire i seguenti comandi:

1. Clonazione del repository
```shell
git clone https://github.com/robertogallea/pulse-workshop.git
```
2. Installazione dipendenze composer
```shell
composer install
```
3. Installazione dipendenze npm
```shell
npm install
```
4. Creazione del database sqlite di test
```shell
touch database/database.sqlite
```
5. Migrazione e seeding del database
```shell
php artisan migrate:fresh --seed
```
6. Esecuzione dell'ambiente di sviluppo
```shell
composer dev
```

### Muoversi nel repository

Ogni commit del repository rappresenta una milestone del workshop.

Per spostarsi da un commit all'altro, eseguire il comando
```shell
git checkout <commit_hash>
```

Elenco degli hash dei commit:
- `4c937be` - Initial commit
- `bc7d5fe` - Pulse installation
- `af5d6c2` - Authorization, users resolution and dashboard customization
- `fb661b5` - Filtering and Performance
- `8819e54` - Custom cards: CustomEvent Card (Event based recorder)
- `645c73c` - Custom cards: Current Weather Card (IsolatedBeat)
