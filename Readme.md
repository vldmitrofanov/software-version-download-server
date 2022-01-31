copy env-example.php to env.php

fill up tokens in env.php

endpoins:

GET 
    /<platform>/download/latest
    /<platform>/check

GET /<platform>/check must have 'version' field. Used to check against latest version. Returns array of versions ahead


POST
    /<platform>

POST must have headers:
    Content-Type: multipart/form-data
    Token: <your secret token>

POST may have 'file' field:
    for mac dmg
    for windows zip
    for linux tar.gz


POST may have update_type field:
    major
    minor
    path
-- default is path

