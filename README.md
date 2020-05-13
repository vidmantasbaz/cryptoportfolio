# cryptoportfolio

to run docker-compose up

to load fixures docker-compose  run php-fpm bin/console doctrine:fixtures:load

endpoints: 

  /api/login -> to login  if fixtures loaded jhon/jhon234
  
  /api/logout -> to logout 

  /api/create -> create assest json request data needed 
  {
	"label": "test ETHt",
	"currency":"ETH",
	"value":8
}

/api/update/{id}  -> to update exsisting asset

/api/delete/{id} -> to delete exsisting asset

/api/get_values/{currency} -> to get values of asset total and individual
