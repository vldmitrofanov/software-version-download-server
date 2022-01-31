software-update.local

endpoins:

GET 
    /mac/download/latest
    /mac/check
    /windows/download/latest
    /windows/check


POST
    /mac
    /windows

Post must have headers:
    Content-Type: multipart/form-data
    Token: <your secret token>

