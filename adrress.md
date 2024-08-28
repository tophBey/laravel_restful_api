# User API Spesifikasi

## User Create

### Method Post
- Endpoint  /api/users/create

- Request Body : application/json
```json
{
"username":"example",
"password":"example",
"name" : "example"
}
```
Succes

Response Body :

- Status code : 201
```json
{
    "data":{
        "id":1,
        "name":"example",
        "username":"example"
    }
}
```

Error

Media Type : application/json

Status Code : 400
```json
{
    "errors":{
        "username":[
            "username must not blank"
        ],
        "name":[
            "name min 6 characters"
        ]
    }
}
```


## Login User

### Method Post

- Endpoint /api/users/login
- Request Body : application/json

```json
{
    "username":"example",
    "password":"example"
}
```
success login

Response Body :
- Status Code : 200
```json
{
    "data":{
        "id":"0",
        "username":"string",
        "name":"string",
        "token":"string", //uniqe
    }
}
```

errors
- Status Code 400
```json
{
    "errors":{
        "massage":[
            "username or password wrong"
        ]
    }
}
```


## Get User

### Method Get
- Endpoint /api/users/current
- Request Body : Token

```json
"Authorization":"value"
```
success login

Response Body :
- Status Code : 200
```json
{
    "data":{
        "id":"0",
        "username":"string",
        "name":"string",
    }
}
```

errors
- Status Code 400
```json
{
    "errors":{
        "massage":[
            "Not found"
        ]
    }
}
```




