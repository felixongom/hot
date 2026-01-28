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

### rundom()
Generate random string of default length 8. Takes in the length of the string as one parameter. 
```php
Hot\Hot::random(5);
```

### env()
It reads a particular evironment variable from the .env file. It returns the value of the key passed. If no key is passed it returns the whole env array results. 
```php
Hot\Data::env(); //returns array;
Hot\Data::env('passcode'); //returns the value of the key;
```
## URL
### redirect()
Redirect to any URL. It takes in the new redirect route, array or string query params and status.
```php
public static function redirect(
        string $url,
        string|array|null $query = null,
        int $status = 302
    ): never 

//usage
Hot\Url::redirect('/login', ['name'=>'tom'], 300)
Hot\Url::redirect('https://www.youtube.com');
```
### redirectBack()
Redirects the request back to the url it is comming from. It takes in array or string query params and status and a status code.
```php
public static function redirectBack(
        string|array|null $query = null,
        int $status = 302
    ):never

//usage
Hot\Url::redirectBack(['name'=>'tom'], 300)
```
### serverBase()
Returns the base URL of the the server you are currently running the website on. Server base URL (domain only)
```php
 public static function serverBase(string|array|null $query = null): string

//usage
Hot\Url::serverBase()
//https://ongom.com
```
### incoming()
Returns the full URL where the request is comming from. Full incoming URL (path + query)
```php
public static function incoming(string|array|null $query = null): string

//usage
Hot\Url::incoming()
//https://ongom.com/home
```
### incomingBase
Base URL of incoming request (includes app subfolder)
```php
public static function incomingBase(string|array|null $query = null): string
//usage
Hot\Url::incomingBase()
```

### incomingPath()
Returns the incoming path only (no scheme, no host)
```php
 public static function incomingPath(string|array|null $query = null): string

//usage
Hot\Url::incomingPath()
```
## Request

### body()
Gets request body from an api call. It can return Object or array or null
```php
public static function body(bool $as_array = true):array|object|null
Hot\Request::body()
```
### params()
Gets request query params. It can return Object or array or null
```php
public static function params(bool $as_array = true):array|object|null
Hot\Request::params()
```
### post()
Gets posted data from a form in post request. It can return Object or array or null
```php
public static function post(bool $as_array = true):array|object|null
Hot\Request::post()
```
### get()
Gets posted data from a form in a get request. It can return Object or array or null
```php
public static function get(bool $as_array = true):array|object|null
Hot\Request::get()
```
### files()
Gets file(s) submited from form or api call. It can return the file or null
```php
public static function files()
Hot\Request::files()
```
## Response

### json()
Converts array or object to json object. Has no effect on numbers and strings.
```php
public static function json($data, int $status_code = 200)
Hot\Response::json()
```

### send()
Converts array or object to json object and send back. Has no effect on numbers and strings.
```php
public static function send($data, int $status_code = 200)
Hot\Response::send()
```

## Files

### upload()
It uploads single or multiple files locally on the server. It also renames the files before saving it. Takes in array parameter,and it retuns a string for single file, array for multiple files and false for failure to upload.
```php
   public static function upload($files, array $options)
$options = [
            'allowed_extension'=>['jpg','png'], //(optional)
            'min_size'=>3, //optionsl in kb (optional)
            'max_size'=>6, //optional in kb (optional)
            'upload_path'=>'path/to/upload/directory', //(optional)
            'new_name'=>'mode_term_2' //optional
            'fixed_name'='any unique upload filename' //optional
            ];
Hot\Files::upload($_FILES['files'], $options);
// min size = 5 and max size = 20
```
### delete()
It deletes any file from the server. It takes in a string or array of filenames and the directory where it is stored.
```php
Hot\Files::delete('logo.png','path/to/directory',); 
//or
Hot\Files::delete(['logo.png','image.jpg'] , 'path/to/directory');
```
### getFiles()
It gets all the files from the provided directory. It take in an array or string of filename, and the derectory where the files is stored and an optional fallback image which can either be a link or the file on the server. 
```php
Hot\Files::getFiles('logo.png','path/to/directory');
//
Hot\Files::getFiles(['logo.png','image.jpg'],'/public/uploads');
//
Hot\Files::getFiles(['logo.png','image.jpg'],'path/to/directory', 'logo.png');
```

### exist()
checks if a file exist in the directory. Takes in the file path and returns a boolean. 
```php
Hot\Files::exist('logo.png','path/to/directory/');

Hot\Files::exist('logo.png','/public/uploads');
```

## Number 

### numberToWords()
Converts number to word upto the range of trillion.
```php
public static function numberToWords(float|int $number): string
// usage
Hot\Number::numberToWords(1002);
//one thousand two 
```


### currency
Converts money to words
```php
public static function currency(float $amount, string $code): string
// usage
Hot\Number::currency(1002, 'USD');
//one thousand two  US dollars
```
### sequence()
Generate and returns sequence of number from down to up or from up to down. Takes in the three number parameter; from, to and steps. 
```php
Hot\Number::sequence(1,8,3);
// OR
Hot\Number::sequence(8, 5, 0.2);
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

* Render template files.
* Use layouts with **slots** as `<?= $$slot ?>` or `<?php echo $$slot ?>` to render content.
* Use layouts optionally and support **nested layouts via component slot**. 
* Automatically echo output via `View::render()` or return as string via `View::fetch()`.
* `name` is a key word that returns the name of the page of component`.

---

## Class: `View`

### Setting Paths

```php
View::setViewPath(__DIR__ . '/views');
View::setLayoutPath(__DIR__ . '/layouts');
View::setComponentPath(__DIR__ . '/views/components');
View::setCachePath(__DIR__ . '/storage/views');
```

* `setViewPath`: Define the directory where your template files reside.
* `setLayoutPath`: Define the directory where your layout files reside.
* `setComponentPath`: Define the directory where your components files reside.
* `setCachePath`: Define the directory where your cache files reside.

---

### Rendering Templates

#### `render()`

```php
View::render(string $view, array $data = [], ?string $layout = null): void
```

* Automatically **echoes** the output.
* Accepts template file name (from `viewPath`).
* Optional layout wraps the content.

**Example:**

```php
// Template file with layout
View::render('home', ['name'=>'Felix'], 'main');
//OR 
View::render('/home', ['name'=>'Felix'], 'main');
```

---

#### `fetch()`

```php
View::fetch(string $view, array $data = [], ?string $layout = null): string
```

* Works like `render()`, but **returns the content as string** instead of echoing.
* Useful for storing or manipulating the rendered content before output.

---

### Template Syntax

#### Variables

* Basic variable: Normal PHP echo syntax, `<?= $var ?>` and `<?php echo $var ?>`
* Nested variables: Access with normal php syntax.

```php
//Escaped automatically
<?= $var ?>
<?php echo $var ?>
<?= $use['name'] ?>

//Raw html content
<?php echo $$htmlContent ?>
<?= $$htmlContent ?>
<?= $$slot ?>

```

---

#### Components / Partials
Set up path to component directory 
```php
  View::setComponentPath(__DIR__ . '/views/components');
```
Create an html file in that directory eg. profile.html, Add content to you html
```php
  <div> profile: <?= $user['name'] ?></div>

  //OR

  <div> 
    <h1><?= $user['name'] ?></h1>
    <h1><?= $user->email ?></h1>
    <?= $$slot ?>  //to dispaly html content within the component tags 
  </div>

  //OR

  <table>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['name'] ?></td>
        </tr>
    <?php endforeach ?>
  </table>

<p>Total users: <?= count($users) ?></p>

```
Display the component within your templete using its name as before

**Example:**
in the templete
```php
<x-profile user="$user" users="$users" count="5" />
//oR
<x-profile user="$user" users="$users" count="5"></x-profile>
//oR
<x-profile user="$user" users="$users" count="5">
  Profile HTML markup
</x-profile>
```
#### Nested components
```php
<x-post-card post="$post">
  <x-image img="$profile_image">
  <x-post img="$post_content">
  display my via slot
</x-profile>

```


### 8️⃣ Component Summary table

| Data type    | Supported | Access in component |
| ------------ | --------- | ------------------- |
| String       | ✅         | `$title`            |
| Integer      | ✅         | `$count`            |
| Boolean      | ✅         | `$isAdmin`          |
| Array        | ✅         | `$users[]`          |
| Object       | ✅         | `$user->name`       |
| Closure      | ✅         | `$callback()`       |
| Slot content | ✅         | `$$slot`             |


#### Available variables in components

- $$slot → inner content

- All passed props → $text, $class, etc.

- Props are unlimited.

- Props are scoped to the component.
---

#### Layouts and Slots

* Use a layout: `View::render('home', $data, 'main')`


* Inside layouts, the **child content is available as `<?= $$slot ?>`** just like in components

```php
<main>
  <?= $$slot ?>
</main>
```

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

### 🚀 View Caching

- Compiled templates are cached to disk for high performance.

#### How it works

- Templates compile once

- Cached PHP files are reused

- Cache invalidates automatically when source files change

```php 
"storage/views/" //must be writable
```

#### 🛡 Security Notes

- `<?= $var ?>` is always escaped

- `<?php echo $var ?>` is raw (use carefully)

- Components and layouts share the same escape rules

- Avoid rendering untrusted content with raw syntax

#### 🛠 Best Practices

- Always prefer `<?= $var ?>` and `<?php echo $var ?>` unless HTML is trusted

- Use components for reusable UI

- Keep layouts minimal

- Do not edit cached files manually

- Clear storage/views during deployments if needed