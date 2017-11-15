# processor-iconv

[![Build Status](https://travis-ci.org/keboola/processor-iconv.svg?branch=master)](https://travis-ci.org/keboola/processor-iconv)

Iconv processor. Takes all CSV files in `/data/in/tables` (except `.manifest` files), converts their encoding to UTF-8 and stores them in `/data/out/tables`. Ignores directory structure and deletes all non-csv files.
 
## Development
 
Clone this repository and init the workspace with following commands:

- `docker-compose build`

Then load some CSV files into `./data/in/tables`, create empty folder `./data/out/tables` and run 

- `docker-compose run --rm -e KBC_PARAMETER_SOURCE_ENCODING=WINDOWS-1250 processor-iconv`
 
# Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-iconv)
 - [Build steps](https://github.com/keboola/processor-iconv/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to ECR if release is tagged
   
# Usage
The processor makes a CSV file orthogonal. It fills missing column names with auto-generated names (`auto_col_XX`) 
and missing values in rows with empty string. The processor is registered with id `keboola-processor.headers`. 
It supports optional parameters:

- `source_encoding` --- required source encoding

See [list of supported encoding](https://gist.github.com/hakre/4188459).

## Sample configurations

Default parameters:

```
{  
    "definition": {
        "component": "keboola-processor.headers"
    },
    "parameters": {
        "source_encoding": "WINDOWS-1250"
    }
}
```
