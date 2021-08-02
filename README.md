## Phoenix pay api ##

les route disponible pour l'api sont les suivante:

### /api/user/create ###
**Methode POst**
utiliser pour la creation des utilisateurs <br>
entete Json:
```
{
	"email": "yamking01@gmail.com",
	"password": "12345678"
}
```
reponse :
```
{
  "type": "success",
  "user": 3
}
```
ou
```
{
  "type": "error",
  "message": "utilisateur existant"
}
```

#### /api/login ####
**Methode POST** 
```
{
	"email": "yamking01@gmail.com",
	"password": "12345678"
}
```
<br>
la requette contien une entete json de ce genre.

elle renvoit une repose 
```
{
    "token: "token"
}
```
 en cas de succes  et 
```
{
  "code": 401,
  "message": "Invalid credentials."
}
```
en cas d'echec
#### /api/transaction/consume ####
**Methode PUT** <br>
cette route est  utiliser pour initier une transaction la transaction les entete sont les suivante:
<br>
**json** 
<br>
et
<br>
**Authorization**:**Bearer token**
<br>
le corp est:
```
{
	"transaction":"id"
}
```
en cas d'echec la reponse est:
```
{
  "type": "error",
  "message": "transaction error"
}
```
et en cas de success la reponse est:
```
{
  "type": "success",
  "message": "transaction error"
}
```
#### /api/transaction ####

    **Methode POST***
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

1. **recever** est l'id du beneficiaire
2. **password** est le mots de pass de l'initiateur
3. **status** est l'etat de la transaction, elle varie de 0-3,
    1. 0: pour transaction annuler,
    2. 1: pour transaction initier
    3. 2: pour transaction en cour
    4. 3: pour transaction terminer
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