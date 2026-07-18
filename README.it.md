# Simple Alexa News Briefing

*[README available in English too](README.md)*

Un plugin WordPress moderno e semplice, basato su REST API, per creare skill di flash briefing Alexa direttamente dai tuoi ultimi articoli. Basato sul codice sorgente originale di Francesco Napoletano.

## Requisiti

- WordPress 6.0 o superiore
- PHP 7.4 o superiore

## Installazione

1. Copia l'intera cartella del plugin in `wp-content/plugins/wp-simplealexa/` sul tuo sito WordPress.
2. Vai su **Plugin** nella dashboard di WordPress e attiva "Simple Alexa News Briefing".
3. Vai su **Impostazioni → Alexa Briefing** e imposta il numero predefinito di elementi.

## Utilizzo

Il plugin espone gli ultimi articoli pubblicati come feed JSON all'indirizzo:

```
https://tuosito.it/wp-json/simplealexa/v1/briefing
```

Ogni elemento include `uid`, `updateDate`, `titleText` e `mainText` (titolo + estratto), conformi ai requisiti delle skill Alexa Flash Briefing.

Usa il parametro query `count` per sovrascrivere il valore predefinito su una singola richiesta, ad esempio `...?count=3`.

## Configurazione

Vai su **Impostazioni → Alexa Briefing** e imposta il numero predefinito di elementi restituiti quando `count` non è presente nella richiesta.

## Lingua

L'interfaccia del plugin è in inglese di default; carica i tuoi file di traduzione `.po`/`.mo` nella cartella `languages/` per localizzarla (text domain: `simplealexa`).

## Aggiornamenti

Il plugin è compatibile con [Git Updater](https://git-updater.com/), quindi può essere tenuto aggiornato direttamente dalla [repository GitHub](https://github.com/gioxx/wp-simplealexa) senza passare da WordPress.org.
