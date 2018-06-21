```

Quick Start
------------------

```
#### git clone https://github.com/T0hha/pd_test_api

#### cd pd_test_api
#### edit config/config.php
#### run 'composer install'
#### run 'php vendor/robmorgan/phinx/bin/phinx migrate'

API
------------------

There is a Pipedrive.postman_collection.json file in the root folder.

OR

#### Example (GET)

http://localhost/api/v1/organizations/Black Banana
http://localhost/api/v1/organizations/Black Banana?page=1

#### Example (POST)

http://localhost/api/v1/organizations/

**Example Payload**

```json
{  
   "org_name":"Paradise Island",
   "daughters":[  
      {  
         "org_name":"Banana tree",
         "daughters":[  
            {  
               "org_name":"Yellow Banana"
            },
            {  
               "org_name":"Brown Banana"
            },
            {  
               "org_name":"Black Banana"
            }
         ]
      },
      {  
         "org_name":"Big banana tree",
         "daughters":[  
            {  
               "org_name":"Yellow Banana"
            },
            {  
               "org_name":"Brown Banana"
            },
            {  
               "org_name":"Green Banana"
            },
            {  
               "org_name":"Black Banana",
               "daughters":[  
                  {  
                     "org_name":"Phoneutria Spider"
                  }
               ]
            }
         ]
      }
   ]
}
```