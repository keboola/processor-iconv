# processor-iconv

[![Build Status](https://travis-ci.org/keboola/processor-iconv.svg?branch=master)](https://travis-ci.org/keboola/processor-iconv)

Iconv processor. Takes all CSV files (or sliced tables) in `/data/in/tables`, converts their encoding to UTF-8 and stores them in `/data/out/tables`.
Manifests (if present) are copied without any change.
 
## Development
 
Clone this repository and init the workspace with following commands:

- `docker-compose build`

### TDD 

 - Edit the code
 - Run `docker-compose run --rm tests` 
 - Repeat
 
# Integration
 - Build is started after push on [Travis CI](https://travis-ci.org/keboola/processor-iconv)
 - [Build steps](https://github.com/keboola/processor-iconv/blob/master/.travis.yml)
   - build image
   - execute tests against new image
   - publish image to ECR if release is tagged
   
# Usage

## Parameters

### source_encoding

Source encoding of the csv file (or sliced table). Destination encoding is always UTF-8.


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

