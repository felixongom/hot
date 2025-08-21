# Hot-lib
This is a php library that provides most of the common controller methods. It has on it static methods like;
### session()
It saves and retrieve session variable. You don't need to start session.

```php
Hot\Hot::session('key', 'value');

Hot\Hot::session('active_user', ['user_id'=>2]);
// will set active_user to the session variable 

Hot\Hot::session('active_user');
// will get active_user from the session variable 
```
### deleteSession()
Will delete the session key from the the session variable. If no parameter is passed, it unset the entire session.

```php
Hot\Hot::deleteSession('active_user');
// will delete session key called active_user from the session variable

Hot\Hot::deleteSession();
// unsets the session.

```

### flash()
It sets and retrieve flash messege. This messege will appear only once. It also uses sessinon under in the background.

```php
Hot\Hot::flash('seccess', 'sent the messege successfully');
// will set flash messege with key success 

Hot\Hot::flash('success');
// will get flash messege with key sucsess  
```
### redirect()
Redirects the request to a new route. It takes in the new redirect route.
```php
Hot\Hot::redirect('/login');
// if using  a route
Hot\Hot::redirect('https://www.youtube.com');
// if using php core without routing library
```
### exist()
Checks if a paticular data exists. it returns boolean. Null, 0 and empty string returns false
```php
Hot\Data::exist('hello');
```
### json()
Converts array to json object. Has no effect on numbers and strings. 
```php
Hot\Data::json(['name'=>'tom']);
```
### send()
Converts array to json object and echo it. Has no effect on numbers and strings. 
```php
Hot\Data::send(['name'=>'tom'], $status_code = 200);
```
### getBody()
Extracting request body from post request. passing false will return an object var
```php
Hot\Data::getBody($as_array = true);
```
### getQuery()
Extracting query string from post request. Passing false will return an object var
```php
Hot\Data::getQuery($as_array = true);
```
### array()
Converts object class array to standard arrays. Has no effect on numbers and strings. 
```php
Hot\Data::array(stdClass Object ([name] => tom [age] => 13));
// ['name'=>'tom', 'age'=>13]
```
### object()
Converts array to standard class object. Has no effect on numbers and strings. 
```php
Hot\Data::object(['name'=>'tom', 'age'=>13]);
// stdClass Object ([name] => tom [age] => 13)
```
### rundom()
Generate random string of default length 8. Takes in the length of the string as one parameter. 
```php
Hot\Hot::random(5);
```
### sequence()
Generate and returns sequence of number from down to up or from up to down. Takes in the three number parameter; from, to and steps. 
```php
Hot\Number::sequence(1,8,3);
// OR
Hot\Number::sequence(8, 5, 0.2);
```
### get()
Returns the varables which are on get superglobal in object form. It takes in $_GET supergloble variable
```php
Hot\Data::get($_GET);

//Also after calling the method, you can access tha keys 
Data::$get->key;
Data::$get->name;
```
### post()
Returns the varables which are on post superglobal in object form. It takes in $_POST supergloble variable
```php
Hot\Data::post($_POST);

//Also after calling the method, you can access tha keys 
Data::$post->key;
Data::$post->name;
```
### upload()
It uploads single or multiple files locally on the server. It also renames the files before saving it. Takes in array parameter,and it retuns a string for single file, array for multiple files and false for failure to upload.
```php
$params = [
                'files'=>'file.png', 
                'allowed_extension'=>['jpg','png'], 
                'min_size'=>3, //optionsl in kb
                'max_size'=>6, //optional in kb
                'upload_path'=>'path/to/upload/directory',
                'new_name'=>'mode_term_2' //optional
            ];
Hot\File::upload($params);
// min size = 5 and max size = 20
```
### delete()
It deletes any file from the server. It takes in a string or array of filenames and the directory where it is stored.
```php
Hot\File::delete('logo.png','path/to/directory',);
//or
Hot\File::delete(['logo.png','image.jpg'] , 'path/to/directory');
```
### files()
It gets all the files from the provided directory. It take in an array or string of filename, and the derectory where the files is stored and an optional falback inmage which can either be a link or the file on the server. 
```php
Hot\File::files('logo.png','path/to/directory');
//
Hot\File::files(['logo.png','image.jpg'],'path/to/directory');
//
Hot\File::files(['logo.png','image.jpg'],'path/to/directory', 'logo.png');
```

### exist()
It gets all the files if the file exist on the server. takes in the file path and returns a boolean. 
```php
Hot\File::exist('path/to/directory/logo.png');
```

### env()
It reads a particular evironment variable from the .env file. It returns the value of the key passed. If no key is passed it returns the whole env array results. 
```php
Hot\Data::env(); //returns array;
Hot\Data::env('passcode'); //returns the value of the key;
```
### format()
It formats the number with commas by default. It takes in the number and an optional formatter, if formatter is not passed, it default to comma.
```php
Hot\Number::format(10000);
// 10,000
Hot\Number::format(10000000, '.');
//10.000.000
```
### matrix()
Converts large numbers to shorthands like 10M for 10000000, 1.3k for 1300, etc. It takes in the number and the optional precision.
```php
Hot\Number::martix(10000);
// 10K
Hot\Number::martix(10000000, 2);
//10.00M
```
### chop()
It cuts some part of the array or string off from some position to a particular position.
```php
Hot\Hot::chop('Test text', 1, 6);
// Test t
Hot\Hot::chop([1,2,3,4,5,6,], 1, 4);
//[1,2,3,4]
```
### trancate()
Instead of rounding off, it trancates the number to some required precision. 
```php
Hot\Number::trancate(15.57, 1);
//15.5
```

### ago()
Takes in the date or time and returns how much time has passed. Returns false if less than 1 seconds.
```php
Hot\Time::ago('2024-11-10 14:00:00');
```

### left()
Takes in the date or time ahead and returns how much time is left.  Returns false if less than 1 seconds. 
```php
Hot\Time::left('2024-11-15 11:38:00');
```

### hash()
Takes in plain text and reterns the hash. There more methods with different algorithms like `bcrypt(123456)`, `algon2i(123456)`, `algon21i(123456)`
```php
Hot\Password::hash('123456');
```

### verify()
Takes in plain text and hashed text then, reterns a boolean, true for match and false for not matching.  
```php
Hot\Password::verify('123455', '$pdojshjs...');
```



<!--  -->
# View Engine

This is a **lightweight, class-based PHP View Engine** that allows you to:

* Render template files, raw strings, or numbers.
* Use layouts with **slots** (default `$slot`).
* Include partials (`@include` or `{{ include() }}`) and pass data.
* Access nested variables via **dot (`user.name`)** or **arrow (`user->name`)** syntax.
* Use layouts optionally and support **nested layouts**.
* Automatically echo output via `View::render()` or return as string via `View::fetch()`.

---

## Class: `View`

### Setting Paths

```php
View::setViewPath(string $path);
View::setLayoutPath(string $path);
```

* `setViewPath`: Define the directory where your template files reside.
* `setLayoutPath`: Define the directory where your layout files reside.

**Example:**

```php
View::setViewPath(__DIR__ . '/views');
View::setLayoutPath(__DIR__ . '/layouts');
```

---

### Rendering Templates

#### `render()`

```php
View::render(string|int $viewOrContent, array $data = [], ?string $layout = null): void
```

* Automatically **echoes** the output.
* Accepts:

  1. Template file name (from `viewPath`).
  2. Raw string template containing `{{ }}`.
  3. Numbers (outputs as string).
* Optional layout wraps the content.

**Example:**

```php
// Template file with layout
View::render('home', ['name'=>'Felix'], 'main');

// Raw string
View::render('<p>Hello {{ name }}</p>', ['name'=>'Alice']);

// Plain number
View::render(12345);
```

---

#### `fetch()`

```php
View::fetch(string|int $viewOrContent, array $data = [], ?string $layout = null): string
```

* Works like `render()`, but **returns the content as string** instead of echoing.
* Useful for storing or manipulating the rendered content before output.

---

### Template Syntax

#### Variables

* Basic variable: `{{ name }}` or `{{ $name }}`
* Nested variables:

```php
{{ user.name }}   // array access
{{ user->email }} // object access
```

---

#### Includes / Partials

* **Directive style**: `@include('partial', ['key'=>'value'])`
* **Function style**: `{{ include('partial', ['key'=>'value']) }}`

**Example:**

```php
@include('partials/header', ['user'=>$name])
{{ include('partials/footer', ['year'=>2024]) }}
```

---

#### Layouts and Slots

* Use a layout: `View::render('home', $data, 'main')`
* Nested layouts inside a layout:

```php
@layout('main')
```

* Inside layouts, the **child content is available as `$slot`**:

```php
<main>
  {{ $slot }}
</main>
```

---

### Internal Methods (Advanced / Optional)

* `compile(string $content)`: Converts template syntax (`{{ }}`, `@include`, `@layout`) into executable PHP.
* `includePartial(string $view, array $data = [])`: Render a partial file with optional data.
* `extendLayout(string $layout, array $data)`: Used internally for nested layouts.

---

### Example Project Structure

```
project/
│── View.php
│── test.php
│── views/
│    ├── home.php
│    ├── user.php
│    └── partial.php
│── layouts/
     ├── main.php
     └── nested.php
```

---

### Sample Templates

**views/home.php**

```php
<h1>Hello {{ user.name }}</h1>
<p>Email: {{ user->email }}</p>
@include('partial', ['msg'=>'From Home'])
```

**layouts/main.php**

```php
<html>
<head><title>{{ title }}</title></head>
<body>
<header>HEADER</header>
<main>{{ $slot }}</main>
<footer>FOOTER</footer>
</body>
</html>
```

**layouts/nested.php**

```php
@layout('main')
<nav>NAVBAR</nav>
<div>{{ $slot }}</div>
```

---

### Usage Examples
You can also use it with any router 
```php
// Set paths
View::setViewPath(__DIR__ . '/views');
View::setLayoutPath(__DIR__ . '/layouts');

// Render template file with layout
View::render('home', ['user'=>['name'=>'Felix','email'=>'f@example.com'], 'title'=>'Home'], 'main');

// Render raw string
View::render('<p>Hello {{ name }}</p>', ['name'=>'Alice']);

// Render number
View::render(12345);

// Nested layout
View::render('home', ['user'=>['name'=>'Dana','email'=>'d@example.com']], 'nested');
```

---

### Notes

* **Dollar sign in variables is optional.** `{{ name }}` or `{{ $name }}` works.
* Supports **both array and object access** in nested variables.
* Both **directive and function style includes** are supported.
* Layouts are **optional** and can extend other layouts.
* `$slot` variable contains the child content inside layouts.
