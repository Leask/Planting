# Friendships

author: Leask Huang

## Get following people
* endpoint: /v1/following/list
* request args:
    - person_id: [int:person_id|str:me]
    - token: [str:token]
* returns:
    - 200: [array:people]


## Unfollow person
* endpoint: /v1/friendships
* method: del
* post json:
    - person_id: [int:person_id]
    - token: [str:token]
* returns:
    - 200
