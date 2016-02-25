# jQuery validator for Laravel 5

This package will provide both client and Server Side validation for laravel 5.

**Dependency**
- Laravel 5
- Jquery 1.11+
- Jquery Validation 1.14

**Usage**
```html
<!-- index.blade.php -->
	<html>
<head>
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.14.0/jquery.validate.min.js"></script>
	<script src="http://cdn.jsdelivr.net/jquery.validation/1.14.0/additional-methods.min.js"></script>
	<script src="validations.js"></script>
</head>
<body>
<form id="form-sample" method="post">

	{{csrf_field()}}

	<table>
		<tr>
			<td>name</td>
			<td><input name="name"></td>
		</tr>

		<tr>
			<td>email</td>
			<td><input name="email"></td>
		</tr>

		<tr>
			<td>age</td>
			<td><input name="age"></td>
		</tr>

		<tr>
			<td>website</td>
			<td><input name="website"></td>
		</tr>

		<tr>
			<td>price</td>
			<td><input name="price"></td>
		</tr>

		<tr>
			<td>highlight_text</td>
			<td><input name="highlight_text"></td>
		</tr>

		<tr>
			<td>display_name</td>
			<td><input name="display_name"></td>
		</tr>

		<tr>
			<td>newsletter</td>
			<td><input name="newsletter" type="radio" value="1"> True <input name="newsletter" type="radio" value="0"> False</td>
		</tr>

		<tr>
			<td>terms</td>
			<td><input name="i_agree" value="yes" type="checkbox"></td>
		</tr>

		<tr>
			<td>  </td>
			<td><input type="submit"></td>
		</tr>

	</table>

</form>
{!! $jquery->validate("#form-sample"); !!}
</body>
</html>

```

```php

// routes.php

Route::any('test', function () {
	
	// validation rules
	$r = [
		'name' => 'required',
		'age' => 'required|min:18|max:21',
		'email' => 'required|email',
		'i_agree' => 'accepted',
		'website' => 'required|url',
		'display_name' => 'required|alpha|between:5,6',
		'price' => 'required|between:100,200',
		'highlight_text' => 'required|between:20,40',
		'newsletter' => 'required|boolean'
	];

	// custom messages
    $m = [
        'required' => 'The field can\'t be left blank',
        'age.min' => 'You must be 18 yrs old',
        'age.max' => 'You must be under 23 yrs',
    ];


	$v = new Artisangang\Validator\Validator($r,$m);
	if (Input::has('name')) {
		$l = $v->make(\Input::all());
		var_dump($l->errors());
	}	

	return view('index');

});

```
