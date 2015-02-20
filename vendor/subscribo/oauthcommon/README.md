# Subscribo OAuth Common Package

Package with functionality related to connecting to 3rd party OAuth services shared by Subscribo API servers and clients (frontend)

## 1. Installation

Note: If other package is adding the dependency for you (which is usually the case), installation is not necessary.

### 1.1 Setup your project's composer.json

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

(Note: you need to have access to this repository as well as to resources it points to)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev"
```

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/oauthcommon": "@dev"
```

and update composer

```sh
    composer update
```

