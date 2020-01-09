# Project Portal API
this EM will allow Research Proejct Portal to connect to REDCap server and get project.

### Url to access the API is: 
https://redcap.stanford.edu/api/?type=module&prefix=project_portal_api&page=services&NOAUTH

#### Required Parameters to get users project information:
1. secret_token(can be optained from the EM configuration on REDCap instance).
2. request: current support is for users only. 
3. users: list of username separated by ','


#####example: 
```
curl --location --request POST 'https://redcap.stanford.edu/api/?type=module&prefix=project_portal_api&page=services&NOAUTH' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'secret_token=[TOKEN]' \
--data-urlencode 'request=users' \
--data-urlencode 'users=username1,username2'
```