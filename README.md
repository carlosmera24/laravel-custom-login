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
3. Instalar Auth con todas los recursos para trabajar solo con bootstrap:
    ```
    php artisan ui bootstrap --auth
    ```
    >También es posible usar:
    >```
    >php artisan ui vue --auth
    >php artisan ui react --auth
    >```
    Qué instala el comando:
    - Controladores:
        - app/Http/Controllers/HomeController.php
        - app/Http/Controllers/Auth/ConfirmPasswordController.php
        - app/Http/Controllers/Auth/ForgotPasswordController.php
        - app/Http/Controllers/Auth/LoginController.php
        - app/Http/Controllers/Auth/RegisterController.php
        - app/Http/Controllers/Auth/VerificationController.php
    - JavaScript:
        - resources/js/bootstrap.js
    - CSS:
        - resources/sass/_variables.scss
        - resources/sass/app.scss
    - Vistas:
        - resources/views/home.blade.php
        - resources/views/auth/login.blade.php
        - resources/views/auth/register.blade.php
        - resources/views/auth/verify.blade.php
        - resources/views/auth/passwords/confirm.blade.php
        - resources/views/auth/passwords/email.blade.php
        - resources/views/auth/passwords/reset.blade.php
        - resources/views/layouts/app.blade.php
    - Rutas:
        - routes/web.php -> Editada, agregando:
            ```
            Auth::routes();

            Route::get('/home', 'HomeController@index')->name('home');
            ```
    A éste punto tenemos instalada toda la estructura necesaria y se han incluido las vistas y accesos para el login y registro, pero no se han cargado los estilos.
4. Generar archivos del Frontend con npm:
    ```
    npm install && npm run dev
    ```
    De ésta manera tenemos aplicados todos los estilos y es funcional el proyecto, pero aún no está creada la base de datos y las tablas requeridas.

## Base de datos

Laravel trabaja con la tabla **users**, su estructura está definida en el archivo _database/migrations/2014_10_12_000000_create_users_table.php_, si ejecutamos ésta migración el proyecto tendría implementado todo lo necesario para registrar usuarios e iniciar sesión, sin embargo la idea es customizar el uso de la base de datos para ello:
1. Crear la base de datos:
    ```
    mysql> CREATE SCHEMA IF NOT EXISTS `custom_login_db` DEFAULT CHARACTER SET utf8;
    ```
2. Agregar credenciales de acceso a la base de datos en el archivo **.env**.
3. Tabla custom:
    - nombre: usuarios
    - Campos: 
        - usuario
        - contrasena
        - estado -> 1: Activo 2: Inactivo
4. Modificar migración:
    >_database/migrations/2014_10_12_000000_create_users_table.php_
    ```
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('usuario')->unique();
            $table->string('contrasena');
            $table->tinyInteger('estado');
        });
    }
    ```
    Ejecutar migración:
    ```
        php artisan
    ```
    El comando ejecutará los otras migraciones creadas por defecto en laravel, correspondiente a las tablas:
    - failed_jobs
    - migrations
    - password_resets
    - usuarios -> Nuestra tabla personalizada.
De esta manera tenemos preparada la base de datos para trabajar, ahora debemos modificar las vistas y estructura para utilizar la tabla personalizada que hemos creado **usuarios**

## Uso de la tabla personalizada "usuarios"
1. Crear usuario inicial con seeder:
    ```
    php artisan make:seeder UsuarioSeeder
    ```
    Agregar al contenido:
    ```
    public function run()
    {
        DB::table('users')->insert(
                                    [
                                        'usuario' => 'demo',
                                        'contrasena' => Hash::make('abcd1234'), // password
                                        'estado' => 1,
                                    ]
                                );

    }
    ```
    Agregar en _database/seeds/DatabaseSeeder.php_ nuestro Seeder:
    ```
    public function run()
    {
        $this->call(UsuarioSeeder::class);
    }
    ```
    Correr el seeder:
    ```
    php artisan db:seed o php artisan migrate:fresh --seed
    ```
2. Crear el modelo para la tabla usuarios:
    ```
    php artisan make:model Usuario
    ```
3. Modificar el modelo Usuario:

    3.1. Agregar las definiciones base:

        protected $table = 'usuarios';

        public $timestamps = false;
        
        protected $attributes = [
            'estado' => 1,
        ];

        protected $fillable = [
            "usuario",
            "contrasena"
        ];

    3.2 Agregar el campo personalizado para la contraseña ya que por defecto Laravel usa **password**:

        ```
        public function getAuthPassword()
        {
            return $this->contrasena;
        }
        ```

    3.3 Agregar las importaciones y extender **Authenticatable** tomando como base *app/User.php*:

        ```
        <?php

            namespace App;

            use Illuminate\Database\Eloquent\Model;
            use Illuminate\Contracts\Auth\MustVerifyEmail;
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Illuminate\Notifications\Notifiable;

            class Usuario extends Authenticatable
            {
                use Notifiable;
        ```

4. Definir el uso del modelo Usuario para la validación en *config/auth.php*:
    ```
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],
    ```
    Cambiar por:
    ```
    'model' => App\Usuario::class,
    ```
5. Indicar al controlador del Login que se utilizará un nombre diferente a **email** para **username**, en nuestro caso **usuario**:
    *app/Http/Controllers/Auth/LoginController.php*
    ```
    /**
     * Get the login username to be used by the controller.
     * @return string
     */
    public function username()
    {
        return 'usuario';
    }
    ```
Hasta aquí estaría listo, sin embargo el formulario solicita un **email** en lugar de **usuario** su validación no permite continuar, esto lo resolvemos cambiando el tipo de campo de *email* a *text* pero login retornará siempre al formulario sin información ya que se envían los parámetros **email** y **password**, y nuestra personalización usa **usuario** y **contrasena**.

## Modificar formulario de login
TODO


    

