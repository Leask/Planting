# Basic Infos Of Planting API v1

author: Leask Huang


## Time

All time strings are ISO-8601.


## Object Schemas

* Person

        #!javascript
        // #K1: 初期可能不实现，根据需要添加
        {
            "id"              : [int],
            "external_id"     : [str],
            "provider"        : [str],
            "screen_name"     : [str],          // length limit: [3, 14]
            "name"            : [str],          // length limit: [1, 21]
            "description"     : [str],          // length limit: 140
            "avatar"          : [str:url],      // length limit: 2048
            "created_at"      : [str:time],
            "updated_at"      : [str:time],
            "status"          : [str],          // verifying / verified
            "timezone"        : [str:timezone], // #K1
            "locale"          : [str:locale],   // #K1
            "class"           : "person"
        }

        // Demo
        {
            "avatar": "",
            "class": "person",
            "created_at": "2013-09-16T18:06:12+0000",
            "description": "",
            "external_id": "xx@leaskh.com",
            "id": 732,
            "locale": "zh_cn",
            "name": "Leask 2x",
            "provider": "email",
            "screen_name": "Leask 2x",
            "status": "normal",
            "timezone": "Asia/Shanghai",
            "updated_at": "2013-09-16T18:06:12+0000"
        }

* Authorization

        #!javascript
        {
            "category": [str],
            "class": "authorization",
            "created_at": "2013-11-01T10:18:35+0000",
            "expires_at": "2014-11-01T10:18:35+0000",
            "person_id": 732,
            "scope": [],
            "token": "705585b4a0967fbba1c8f9764d6d06d4d0061227da7062d46ddbfbc8fdd6b46a"
        }

        // Demo
        {
            "category": "person",
            "class": "authorization",
            "created_at": "2013-11-01T10:18:35+0000",
            "expires_at": "2014-11-01T10:18:35+0000",
            "person_id": 732,
            "scope": [],
            "token": "705585b4a0967fbba1c8f9764d6d06d4d0061227da7062d46ddbfbc8fdd6b46a"
        }
