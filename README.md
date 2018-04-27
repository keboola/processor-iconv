# processor-iconv

[![Build Status](https://travis-ci.org/keboola/processor-iconv.svg?branch=master)](https://travis-ci.org/keboola/processor-iconv)

[Iconv](http://php.net/manual/en/function.iconv.php) processor. Takes all CSV files (or sliced tables) in `/data/in/tables` and `/data/in/files`, converts their encoding to destination encoding and stores them in `/data/out/tables` or `/data/out/files`. Manifests (if present) are copied without any change. Sliced files are supported.

# Usage
The processor takes three options:

- `source_encoding` - required string, [encoding](https://gist.github.com/hakre/4188459) of the source files/tables, must be same for all files/tables
- `target_encoding` - optional string, [encoding](https://gist.github.com/hakre/4188459) of the target files/tables, defaults to `UTF-8`
- `on_error` - optional string, can be either `transliterate` or `ignore` or `fail`, defines the action to do when a character cannot be converted. The default value is `fail`, which means that the conversion will fail.

## Charset Conversion

Example configuration which converts everything from `WINDOWS-1250` to `UTF-8`:

```
{
    "definition": {
        "component": "keboola.processor-iconv"
    },
    "parameters": {
        "source_encoding": "WINDOWS-1250"
    }
}
```

## UTF-8 Sanitization

Example configuration which sanitizes invalid `UTF-8` characters and sequences:

```
{
    "definition": {
        "component": "keboola.processor-iconv"
    },
    "parameters": {
        "source_encoding": "UTF-8",
        "target_encoding": "UTF-8",
        "on_error": "ignore"
    }
}
```

For more information about processors, please refer to [the developers documentation](https://developers.keboola.com/extend/component/processors/).

## Development

Clone this repository and init the workspace with following command:

```
git clone https://github.com/keboola/processor-iconv
cd processor-iconv
docker-compose build
docker-compose run --rm dev composer install --no-scripts
```

Run the test suite using this command:

```
docker-compose run --rm dev composer tests
```

# Integration

For information about deployment and integration with KBC, please refer to the [deployment section of developers documentation](https://developers.keboola.com/extend/component/deployment/)
