
## Loyalty Points API

The loyalty-points-api is created to enable businesses("merchants") to give loyalty points to their customers

This API performs the following tasks

- Creation and authentication of a user
- Creation and authentication of a customer
- Creation and management of Merchant Accounts
- Creation and management of Programs ie campaigns by the merchant accounts
- Subscription to the program created by a customer
- Earning and spending of loyalty points by a customer
- Management of transactions made by a customer


### Creation and authentication of a User

User created using the API have the following roles

- Admin
- Customer
- Merchant

### API Reference

#### User Registration
```bash
   POST /api/v2/registration
```
#### request
```json
  {
    "id": 0, 
    "first_name": "test",
    "last_name": "test",
    "username": "test",
    "email": "test@gmail.com",
    "password": "test",
    "phone_prefix": "25**",
    "mobile": "77*******",
    "user_type": "customer or  merchant"
}
```
#### response
```json
  {
    "success": true,
    "data": {
        "email": "test@gmail.com",
        "username": "test",
        "message": "User created and verfication email sent to test@gmail.com"
    },
    "status": 201
}
```
>All fields are **required**,meaning if any of them is not provided,bellow is the response  with a status code of **422 Unproccessable Content** for example **email**
#### response
```json
 {
    "message": "The email field is required.",
    "errors": {
        "email": [
            "The email field is required."
        ]
    }
}
```
#### User Login
```bash
   POST /api/v2/login
```
#### request
```json
 {
    "email": "test@gmail.com", 
    "password": "test", 
}
```
#### response
```json
  {
    "success": true,
    "data": {
        "id": 1,
        "email": "test@gmail.com",
        "username": "test",
        "token": "token",
        "user_type": "merchant"
    },
    "status": 201
}
```
>All fields are **required**,meaning if any of them is not provided,bellow is the response  with a status code of **422 Unproccessable Content** for example **email**,**password**
#### response
```json
 {
    "message": "The email field is required. (and 1 more error)",
    "errors": {
        "email": [
            "The email field is required."
        ],
        "password": [
            "The password field is required."
        ]
    }
}
```
>If the email provided doesnt match with a user,bellow is the response with a status code of **401 Unauthorized**
#### response
```json
 {
    "success": false,
    "data": "No user with email xxx@gmail.com",
    "status": 401
}
```
>If the password  provided doesnt match with a user,bellow is the response with a status code of **401 Unauthorized**
#### response
```json
{
    "success": false,
    "data": "Wrong Password",
    "status": 401
}
```
### Creation and authentication of a Customer



