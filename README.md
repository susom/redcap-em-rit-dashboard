# Project Portal API
this EM will allow Research Proejct Portal to connect to REDCap server and get project.

### Url to access the API is: 
http://[HOST_NAME]/external_modules/?prefix=project_portal_api&page=services&NOAUTH

#### Required Parameters to get users project information:
1. secret_token(can be optained from the EM configuration on REDCap instance).
2. request: current support is for users only. 
3. users: list of username separated by ','


#####example: 
```angular2
curl --location --request POST 'http://redcap.stanford.edu/external_modules/?prefix=project_portal_api&page=services&NOAUTH' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'secret_token=[TOKEN]' \
--data-urlencode 'request=users' \
--data-urlencode 'users=username1,username2'
```