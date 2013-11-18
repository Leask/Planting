# People

author: Leask Huang


## Signin
* endpoint: /v1/people/signin
* method: post
* post json A:
    - provider: [str:email|...]
    - external_id: [str:external_id]
    - password: [str:password]
* post json B:
    - screen_name: [str:screen_name]
    - password: [str:password]
* returns:
    - 200: [object:authorization]


## Signup
* endpoint: /v1/people/signup
* method: post
* post json:
    - screen_name: [str:screen_name]
    - provider: [str:email|...]
    - external_id: [str:external_id]
    - password: [str:password]
* returns:
    - 200: [object:authorization]


## Profile
* endpoint: /v1/people/show
* method: get
* query args:
    - person_id: [int:person_id|str:me]
    - user_token: [str] // [OPTIONAL]
* returns:
    - 200: [object:person]
