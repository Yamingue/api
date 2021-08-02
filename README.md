## Phoenix pay api ##

les route disponible pour l'api sont les suivante:

#### /api/login ####
{
	"email": "yamking01@gmail.com",
	"password": "12345678"
}

la requette contien une entete json de ce genre.

elle renvoit une repose {"token: "token"} en cas de succes  et 
{
  "code": 401,
  "message": "Invalid credentials."
}
en cas d'echec

#### /api/trans ####
cette route est  utiliser pour initier une transaction la transaction les entete sont les suivante:
**json** 
et
**Authorization**:**Bearer token**
le corp est:
```
{
	"recever": 1,
	"password":"12345678",
	"status": 3,
	"amount": 11
	
}
```

**recever** est l'id du beneficiaire
**password** est le mots de pass de l'initiateur
**status** est l'etat de la transaction, elle varie de 0-3,
    0: pour transaction annuler,
    1: pour transaction initier
    2: pour transaction en cour
    3: pour transaction terminer
en cas de succes la reponce est de la suivande 
```
{
    "type": "sucess",
    "message": "transastion done id:4"
}
```
ou
```
{
    "type": "sucess",
    "message": "ransaction create. transaction id:4"
}
```
en cas d'echec :
```
{
  "type": "error",
  "message": "message d'erreur"
}
```