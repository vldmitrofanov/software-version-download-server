
endpoins:

GET 
    /mac/download/latest
    /mac/check
    /windows/download/latest
    /windows/check


POST
    /mac
    /windows

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

