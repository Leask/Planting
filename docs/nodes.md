# Nodes

author: Leask Huang


## Get a node [未实现]
* endpoint: /v1/nodes/[id]


## Delete a node [未实现]
* endpoint: /v1/nodes/[id]


## Plant a node (with [未实现] / without reply ID)
* endpoint: /v1/nodes
* method: post / put
* request args:
    - token: [str:token]
* post json:
    - what: [str]
    - when: [str:time]
* returns:
    - 200: [object:node]


## Get caring nodes by person id
* endpoint: /v1/nodes/caring
* method: get
* request args:
    - person_id: [int:person_id|str:me]
    - user_token: [str] // [OPTIONAL]
* returns:
    - 200: [array:node_objects]


## Home nodes feed
* endpoint: /v1/nodes/home
* method: get
* request args:
    - user_token: [str] // [OPTIONAL]
* returns:
    - 200: [array:node_objects]
