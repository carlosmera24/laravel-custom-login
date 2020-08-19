# Laravel Custom Login
Implementación de login utilizando Laravel **7.25.0**:
1. Utilizando la base de laravel para Auth se utiliza tabla personalizada para el registro de usuarios y sus campos.
2. Se utiliza todo la parte gráfica y recursos provistos por Laravel Auth
3. Migraciones.

Es importante destacar que **Laravel** utiliza la tabla **user** para 

## Implementación:
1. Partimos de una instalación limpia de Laravel:
    ```
    mkdir laravel-custom-login
    composer create-project --prefer-dist laravel/laravel laravel-custom-login
    ```
2. Instalar **laravel/ui** el cual nos permitirá instalar Auth.
    ```
    composer require laravel/ui    
    ```
    >Recordemos que en la versión **6** laravel optó por trasladar la decisión de usar pre-procesador, framework de Javascript y CSS al desarrollador, es por ello que la funcionalidad para front-end (Boostrap, Vue) fue movido al paquete composer oficial [**laravel/ui**](https://laravel.com/docs/7.x/frontend#introduction)
3. Instalar Auth con todas los recursos:
    ```
    php artisan ui vue --auth
    ```
    >También es posible usar:
    >```
    >php artisan ui bootstrap --auth
    >php artisan ui react --auth
    >```
    Qué instala el comando:
    - 
    

