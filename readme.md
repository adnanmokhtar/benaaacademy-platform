# BenaaacademyPlatform





#### 
> A modular multilingual CMS built with Laravel 5 introduce a full-featured modular multilingual CMS built on top of the Laravel framework.

### Minimum System Requirements:

To be able to run Benaaacademycms you have to meet the following requirements:

- PHP 8.1 or higher
- PDO PHP Extension
- Mcrypt PHP Extension
- GD PHP Library
- MySql 5.5




### Installing BenaaacademyCms:

It's very easy, you can choose one of three installation methods:

First, you must have a laravel 5 project you can install Benaaacademy/platform package

	composer require Benaaacademy/platform

Then add service provider in app config

	Benaaacademy\Platform\ServiceProvider::class

Then Run this artisan command to install

	php artisan Benaaacademy:install


Enjoy :)

---

You can now login /backend with your username and password asked during the install command. After you've logged in you'll be able to access the administration panel on the /backend URI.

Also you can change admin url prefix from admin config:

	<?php

	return [

    	/**
     	* | Admin prefix (the admin url segment)
     	* |
     	* | @var string
     	*/

    	'prefix' => env("ADMIN_PREFIX", "backend"),

    	/**
     	* | Default URI after user authentication
     	* | without admin prefix
     	* | @var string
     	*/

    	'default_path' => env("DEFAULT_PATH", "posts"),


### Plugin structure:

Plugins folder is located at root directory besides app folder.

Each plugin may contain these directories:

- `config`
- `controllers`
- `lang`
- `models`
- `migrations`
- `views`
- `routes.php`
- `{Name}Plugin.php` 

### Creating plugin
	
	php artisan plugin:make plugin-name --plain

you can also create a plugin with some extra resources.

	php artisan plugin:make plugin-name --resources
	
	
By default the plugin is uninstalled. To install Run: 

	php artisan plugin:install plugin-name
	


###Plugin bootstrap file class

Naming this file will be the upper camel case of plugin folder suffixed with Plugin.php

Example:

 - The plugin file with folder name "products" will be ProductsPlugin.php and the plugin file with folder name "featured_products" will be FeaturedProductsPlugin.php.

The plugin Class name is the same as plugin file name.


####A sample "products" plugin

 
 	<?php

	/*
 	 * Class ProductsPlugin
 	 */
 	
	class ProductsPlugin extends Plugin {

    	/** [optional]
     	* | Plugin Service providers
     	* |
     	* | @var array
     	*/
    	public $providers = [];

    	/** [optional]
     	* | Plugin aliases
     	* |
     	* | @var array
     	*/
    	public $aliases = [];

    	/** [optional]
     	* | Admin commands
     	* | Located in plugin commands folder
     	* | @var array
     	*/
    	public $commands = [];

    	/** [optional]
    	* | plugin middlewares are located in plugin middlewares folder
     	* | @var array
     	*/
    	public $middlewares = [
    		//PluginMiddleware::class
    	];

       /** [optional]
     	* | Plugin routed middleware.
     	* | Located in plugin middlewares folder
     	* | These middleware may be assigned to groups or used individually.
     	* |
     	* | @var array
     	*/
    	public $route_middlewares = [];

    	/** [optional]
     	* | Plugin permissions
     	* | A list of plugin permissions
     	* | @var array
     	*/
    	public $permissions = [
    		// "create"
    	];


    	/** [required]
     	* Plugin details
     	* @return array
     	*/
    	function info(){

        	return [
            	'name' => 'products',
            	'description' => '',
            	'version' => '0.1',
            	'icon' => 'fa-puzzle-piece',
            	'author' => '',
            	'url' => ''
        	];

    	}

    	/** [required]
     	* Plugin bootstrap
     	* Called in system boot
     	*/
    	function boot(){
	
    	}	

    	/** [optional]
     	* Plugin install
     	* Running plugin migrations and default options
     	*/
    	function install(){
        	parent::install();
    	}

    	/** [optional]
    	* Plugin uninstall
    	* Rollback plugin installation
    	*/
    	function uninstall(){
       		parent::uninstall();
    	}

	}

	

- Plugin info method return an array of plugin details.
- Plugin install and uninstall methods are called when installing and uninstalling plugin.
- Boot method is called when system booted.
- In boot method you can add some functionalites such as:
	- Attaching tasks to system Actions.
	- Creating items in sidebar menu.
	- Add some urls to sitemap file.
	- Creating some schedule tasks.
	- Adding widgets to dashboard.
	- Adding extra html inputs to admin forms.
	- Add some plugin helper functions.
	

###Config directory:

After creating plugin using command line, a new file named with module name is placed in config folder may contain these default options

You can access plugin config item value using:

	config("module_name.config_key");

    
#### Plugin Controllers directory

-  Created contollers should be extended from `BackendController` class.


#### Plugin Models directory

-  Created models should be extended from `Model` class.

######  This is a sample model:

	<?php

	/**
	 * Class Product
	 */
	class Product extends Model
	{

    /**
     * @var string
     */
    protected $module = 'products';

    /**
     * @var string
     */
    protected $table = 'products';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     * specify table fields to search using built-in scopeSearch()
     */
    protected $searchable = ['title', 'content'];
    
    /**
     * @var array
     * make slug from title and store it in slug field before creating a record
     */
    protected $sluggable = ['slug' => 'title'];

    /**
     * @var array
     */
    protected $creatingRules = [
		'title' => 'required'
    ];

    /**
     * @var array
     */
    protected $updatingRules = [
		'title' => 'required'
    ];
    
  
    ?>
    
  
###### Custom plugin model validation:

	<?php
	
	 /*
	 * Adding some custom messages to validator
     * @return array
     */
    protected function setValidationMessages()
    {
        return [
        	"name.required" => "The name is required",
        	// and more
        ];
    }

    /**
     * Adding attributes names to validator
     * @return array
     */
    protected function setValidationAttributes()
    {
        return [
        	"name" => "Client name",
        	// and more
        ]
    }
	
     /**
     * @param $v
     * @return mixed
     * run when creating or updating a model
     */
    function setValidation($v)
    {

        $v->sometimes("content", "required", function ($input) {
            return $input->type == "featured";
        });
        
        return $v;
    }  
    
    /**
     * @param $v
     * @return mixed
     * run when creating a model
     */
    function setCreateValidation($v)
    {

        $v->sometimes("content", "required", function ($input) {
            return $input->type == "featured";
        });
        
        return $v;
    }  
    
    /**
     * @param $v
     * @return mixed
     * run when updating a model
     */
    function setUpdateValidation($v)
    {

        $v->sometimes("content", "required", function ($input) {
            return $input->type == "featured";
        });
        
        return $v;
    }  
    
    
###### Saving a model:

	 <?php

     $product = new Product();

     $product->title = "A sample title";
     $product->content = "A sample content";
     
     
     // overriding model validation "if wanted"
     $product->rules(["title" => "required"], ["title.required" => "Missing title"]);
     
     if (!$product->validate()) {
        return $product->errors();	// return validation errors
     }
     
     $product->save();	
     // Return id
     // A unique slug will be created and stored without assigning it.
     
 
###Migrations directory:

You can create a new plugin migration file using

	php artisan plugin:migration {migration_file_name} {module_name}

And run plugin migration

	php artisan plugin:migrate {module_name}
	
	
###Views directory:

View are accessed from anywhere using 

	return view({module_name}::{view_file_name}); 

###Lang directory:

You can access plugin lang item using:

	trans("{module_name}::{lang_file}.{lang_key}");
	
Some extra keys are created to translate module name, permissions, attributes and custom validation messages

	<?php
	
	'module' => '{module_title}',
	
	'attributes' => [
	    
	    'title' => 'Title',
	    
	    // some other fields
	],

	'permissions' => [
	    
	    'create_item' => 'Create an item',
	    
	    // some other messages
	],
	
	'messages' => [
	    
	    'required' => 'This field is required',
	    
	    // some other messages
	]	
	
	?>
	

###Routes file:

	<?php
	
	Route::group([
    
    	"prefix" => ADMIN,
    
    	"middleware" => ["web", "auth"],
	
	], function ($route) {
	
			// routes goes here
			
	});



### Actions:

- Actions are the hooks that the cms core launches at specific points during execution, or when specific events occur. Plugins can specify that one or more of its PHP functions are executed at these points, using the Action API.

- Actions are built on [laravel events](https://laravel.com/docs/5.2/events).

---

#####Example:
	
	<?php
	
	// Using callbacks
	Action::listen('auth.login', function($user){
		// do some tasks
	});
	
	// Or call `onLogin` method in `LoginHandler` class
	Action::listen('auth.login', 'LoginHandler@onLogin');
	
	// Or call `handle` method in `LoginHandler` class
	Action::listen('auth.login', 'LoginHandler');
	
	// Adding Action priority if there are more than one listener
	Action::listen('auth.login', 'LoginHandler', 1);


Developers may need to add some fields to admin forms.
Let's do an example with actions.

#####Example: Adding gender field to users:

We will add this code to plugin class boot method to add the the html view of field using `user.form.featured` action.

	<?php
	
	Action::listen("user.form.featured", function ($user) {
    
    	return view("users::custom")->with("user", $user);
    	
	});

In `custom` view, we will add the required gender field.

	<div class="form-group">
    	<input name="gender"
           value="<?php echo @Request::old("gender", $user->gender); ?>"
           class="form-control input-lg" />
	</div>
	
Almost done, just save it using `user.saving` action.

	<?php
	
	Action::listen("user.saving", function ($user) {
        
        // Adding gender field to model attributes before saving it.
        $user->gender = Request::get("gender");
        
        // some validation rules if wanted
        $user->rules(
        	["gender" => "required"],
        	["gender.required" => "Gender field is required"], // optional
        	["gender" => "Gender"]	// optional
        );
        
    });

#### Actions API:

	<?php

	// Authentication
	Action::listen("auth.login", function($user){});
	Action::listen("auth.logout", function($user){});
	Action::listen("auth.forget", function($user){});
	Action::listen("auth.reset", function($user){});
	
	// Users
	Action::listen("user.saving", function($user){});
	Action::listen("user.saved", function($user){});
	Action::listen("user.deleting", function($user){});
	Action::listen("user.deleted", function($user){});
	Action::listen("user.form.featured", function($user){});
	Action::listen("user.form.sidebar", function($user){});
	
	// Roles
	Action::listen("role.saving", function($role){});
	Action::listen("role.saving", function($role){});
	Action::listen("role.deleting", function($role){});
	Action::listen("role.deleted", function($role){});
	Action::listen("role.form.featured", function($role){});
	Action::listen("role.form.sidebar", function($role){});
	
	// Pages
	Action::listen("page.saving", function($page){});
	Action::listen("page.saving", function($page){});
	Action::listen("page.deleting", function($page){});
	Action::listen("page.deleted", function($page){});
	Action::listen("page.form.featured", function($page){});
	Action::listen("page.form.sidebar", function($page){});
	
	// Posts
	Action::listen("post.saving", function($post){});
	Action::listen("post.saving", function($post){});
	Action::listen("post.deleting", function($post){});
	Action::listen("post.deleted", function($post){});
	Action::listen("post.form.featured", function($post){});
	Action::listen("post.form.sidebar", function($post){});
	
	// Tags
	Action::listen("tag.saving", function($tag){});
	Action::listen("tag.saving", function($tag){});
	Action::listen("tag.deleting", function($tag){});
	Action::listen("tag.deleted", function($tag){});
	Action::listen("tag.form.featured", function($tag){});
	Action::listen("tag.form.sidebar", function($tag){});
	
	// Categories
	Action::listen("category.saving", function($category){});
	Action::listen("category.saving", function($category){});
	Action::listen("category.deleting", function($category){});
	Action::listen("category.deleted", function($category){});
	Action::listen("category.form.featured", function($category){});
	Action::listen("category.form.sidebar", function($category){});
	
	// Dashboard
	Action::listen("dashboard.featured", function(){});
	Action::listen("dashboard.right", function(){});
	Action::listen("dashboard.middle", function(){});
	Action::listen("dashboard.left", function(){});
	
	

### Creating items in sidebar menu:


	<?php
	
	Navigation::menu("sidebar", function ($menu) {
    	
    	$menu->item('products', trans("products::products.name"), URL::to(ADMIN . '/products'))
    	
    	->icon("fa-product-hunt")	// optional
    	
    	->order(1);				// optional
	});
	
	
- Method `item` accepts slug, title and url. 
- As the above example, you can make a sub item from by making child item slug to `products.{sub_item}` 
- Slug parameter must be unique.


### Creating schedule tasks:


	<?php
	
	Schedule::run(function($schedule){
   	 	
   	 	$schedule->call(function(){
        	
        	// Executing some db queries
        	
        	sleep(7);
        	
    	})->cron('* * * * *')->name("task_name")->withoutOverlapping();
	
	});


### Adding links to sitemap:

	<?php
	
	Sitemap::set("sitemap", function($sitemap){
		
		$sitemap->url(url("/"))
		
		->date(time())		// optional
		
		->priority("0.9")	// optional
		
		->freq("hourly")	// optional
	});

This url will be appended in main sitemap `sitemap.xml` in `public/sitemaps` directory.


### Commands:

There are some commands to help you to giva a fast work with plugins

	plugin:list          List all plugins
	plugin:make          make a plugin (--plain & --resources)
	plugin:migration     Create a plugin migration file
	plugin:migrate       Migrate plugin migration files
	plugin:migrate:up    Make plugin migrations up
	plugin:migrate:down  Make plugin migrations down
	plugin:publish       Publishing plugin public and config files
	plugin:install       Install a plugin
	plugin:uninstall     Uninstall a plugin
	plugin:update        reinstall a plugin

### And more..

`Dont forget to send a feedback..`
