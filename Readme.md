copy `env-example.php` to `env.php`

fill up tokens in env.php

#Endpoins:

```
GET
    /<platform>/download/latest
    /<platform>/check
```

```
POST
    /<platform>
```

## Notes

### GET:
```
GET /<platform>/check must have 'version' field. 
Used to check against latest version. 
Returns array of versions ahead
```

### POST:

```
POST /<platform> must have headers:
    Content-Type: multipart/form-data
    Token: <your secret token>
```
```
POST /<platform> may have 'file' field:
    for darwin dmg
    for windows zip
    for linux tar.gz
```

```
POST /<platform> may have update_type field:
    major
    minor
    path
-- default is 'path'

OR use version='2.0.14' to set exact version
```

