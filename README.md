# Project Portal API
this EM will allow Research Proejct Portal to connect to REDCap server and get project.

### Url to access the API is: 
https://redcap.stanford.edu/api/?type=module&prefix=project_portal_api&page=services&NOAUTH

#### Required Parameters to get users project information:
1. secret_token(can be optained from the EM configuration on REDCap instance).
2. request: users
3. users: list of username separated by ','


#####example: 
```
curl --location --request POST 'https://redcap.stanford.edu/api/?type=module&prefix=project_portal_api&page=services&NOAUTH' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'secret_token=[TOKEN]' \
--data-urlencode 'request=users' \
--data-urlencode 'users=username1,username2'
```

#### Required Parameters to link Project Portal into REDCap:
1. secret_token(can be optained from the EM configuration on REDCap instance).
2. request: add_project
3. redcap_project_id: the unique ID for REDCap so we can save the information into Project settings for Project Portal API EM
4. project_portal_id: unique identifier for Project Portal project
5. project_name: project portal name
6. project_description: project portal description
7. project_url: project portal url


#####example: 
```
curl --location --request POST 'http://ihabz.stanford.edu/api/?type=module&prefix=project_portal_api&page=services&NOAUTH' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'secret_token=[TOKEN]' \
--data-urlencode 'request=add_project' \
--data-urlencode 'redcap_project_id=[REDCAP_PROJECTID]' \
--data-urlencode 'project_portal_id=2' \
--data-urlencode 'project_name=[PROJECT_PORTAL_NAME]' \
--data-urlencode 'project_description=[PROJECT_PORTAL_DESCRIPTION]' \
--data-urlencode 'project_url=[PROJECT_PORTAL_URL]'
```


