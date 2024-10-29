## Pulse Workshop

Repository per il workshop su Laravel Pulse organizzato dal Grusp a Verona il giorno 8 novembre 2024.

### Istruzioni per l'installazione

Per installare l'applicazione di esempio eseguire i seguenti comandi:

1. Clonazione del repository
```shell
git clone https://github.com/robertogallea/pulse-workshop.git
```
2. Spostati all'interno del progetto
```shell
cd pulse-workshop
```
3. Copia il file di env
```shell
cp .env.example .env
```
4. Installazione dipendenze composer
```shell
composer install
```
5. Genera una chiave di applicazione
```shell
php artisan key:generate
```
6. Installazione dipendenze npm
```shell
npm install
```
7. Creazione del database sqlite di test
```shell
touch database/database.sqlite
```
8. Migrazione e seeding del database
```shell
php artisan migrate:fresh --seed
```
9. Esecuzione dell'ambiente di sviluppo
```shell
composer dev
```

### Muoversi nel repository

Ogni commit del repository rappresenta una milestone del workshop.

Per spostarsi da un commit all'altro, eseguire il comando
```shell
git checkout <commit_hash>
```

L'elenco degli hash dei commit verrà riportato in questa pagina al termine del corso.
