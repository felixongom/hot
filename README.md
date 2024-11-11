# Hot
This is a php library that provides most of the common controller methods. It has on it static methods like;
### session()
It saves and retrieve session variable. You don't need to start session.

```php
Hot\Hot::session('active_user', ['user_id'=>2]);
// will set active_user to the session variable 

Hot\Hot::session('active_user');
// will get active_user from the session variable 
```
### deleteSession()
Will delete the session key from the the session variable. if no parameter is passed, it unset the session entire session.

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
Checks if a paticular data exists. it returns boolean. Null, 0, empty and string returns false
```php
Hot\Hot::exist('hello');
```
### json()
Converts array to jason object. Has no effect on numbers and strings. 
```php
Hot\Hot::json(['name'=>'tom']);
```
### send()
Converts array to jason object and echo it. Has no effect on numbers and strings. 
```php
Hot\Hot::send(['name'=>'tom']);
```
### array()
Converts object class array to standard arrays. Has no effect on numbers and strings. 
```php
Hot\Hot::array(stdClass Object ([name] => tom [age] => 13));
// returns ['name'=>'tom', 'age'=>13]
```
### object()
Converts array to standard class object. Has no effect on numbers and strings. 
```php
Hot\Hot::object(['name'=>'tom', 'age'=>13]);
// returns stdClass Object ([name] => tom [age] => 13)
```
### rundom()
Generate random string of default length 8. Takes in the length of the string as one parameter. 
```php
Hot\Hot::random(5);
```
### numbers()
Generate and returns sequence of number from down to up or from up to down. Takes in the three number parameter; from, to and steps. 
```php
Hot\Hot::random(1,8,3);
// OR
Hot\Hot::random(8, 5, 0.2);
```
### get()
Returns the varables which are on get superglobal in object form. It takes in $_GET supergloble variable
```php
Hot\Hot::get($_GET);
```
### post()
Returns the varables which are on post superglobal in object form. It takes in $_POST supergloble variable
```php
Hot\Hot::post($_POST);
```
### upload()
It uploads single or multiple files locally on the server. It also renames the files before saving it. Takes in for parameters, the file, upload pathe, array allowed extension minimum accepted file size and maximum accepted file size. and it retuns a string for single file, array for multiple files and false for failure to upload.
```php
Hot\Hot::upload($_FILES['image'], 'path/to/upload/directory', ['jpg','png'], 5, 20);
// min size = 5 and max size = 20
Hot\Hot::upload($_FILES['image'], 'path/to/upload/directory');
//last three parameters are optional.
```
### delete()
It deletes any file from the server. It takes in a string or array of filenames and the directory where it is stored.
```php
Hot\Hot::upload('path/to/directory', 'logo.png');
//or
Hot\Hot::upload('path/to/directory', ['logo.png','image.jpg']);
```
### files()
It gets all the files from the provided directory. It take in an array or string of filename, and the derectory where the files is stored and an optional falback inmage which can either be a link or the file on the server. 
```php
Hot\Hot::files('path/to/directory', 'logo.png');
//
Hot\Hot::files('path/to/directory', ['logo.png','image.jpg']);
//
Hot\Hot::files('path/to/directory', ['logo.png','image.jpg'], 'logo.png');
```

### fileExist()
It gets all the files if the file exist on the server. takes in the file path and returns a boolean. 
```php
Hot\Hot::fileExist('path/to/directory/logo.png');
```

### env()
It reads a particular evironment variable from the .env file. It returns the value of the key passed. If no key is passed it returns the whole env array results. 
```php
Hot\Hot::env(); //returns array;
Hot\Hot::env('passcode'); //returns the value of the key;
```
### format()
It formarts the number with commas by defalt. It takes in the number and an optional formatter, if formatter is nt passed, it default to comma.
```php
Hot\Hot::format(10000);
// 10,000
Hot\Hot::format(10000000, '.');
//10.000.000
```
### matrix()
Converts large numbers to shorthands like 10M or 100000, 1.3k for 1300, etc. It takes in the number and the optional precision.
```php
Hot\Hot::martix(10000);
// 10K
Hot\Hot::format(10000000, 2);
//10.00M
```
### chop()
It cuts some part of the array or strin off from some position to a particular position.
```php
Hot\Hot::chop('Test text', 1, 6);
// Test t
Hot\Hot::chop([1,2,3,4,5,6,], 1,4);
//[1,2,3,4]
```
### trancate()
Instead of rounding off, it trancates the number to some required precision. 
```php
Hot\Hot::trancate('Test text', 1, 6);
// Test t
Hot\Hot::trancate([1,2,3,4,5,6,], 1,4);
//[1,2,3,4]
```