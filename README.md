# cryptoportfolio

to run docker-compose up

to load fixures docker-compose  run php-fpm bin/console doctrine:fixtures:load
to run tests docker-compose  run php-fpm bin/phpunit


endpoints: 

#  /api/login 
   to login  if fixtures loaded use credentials: jhon/jhon234
  
#  /api/logout  
   To logout with current user
  

  #/api/create 
   Create assest json request data needed 
   ```json
  {
	"label": "test ETHt",
	"currency":"ETH",
	"value":8
}
```

#/api/update/{id}
  
  Update existing asset

#/api/delete/{id} 

Delete existing asset

#/api/get_values/{currency} 

Get values of asset total and individual
```json
{
    "BTC": {
        "value": "19",
        "rate_usd": 9520.8,
        "usd": 180895.2
    },
    "Total": 180895.2
}
```
