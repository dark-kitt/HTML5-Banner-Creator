<?php

	/**
	*
	*/

	if ( isset( $_POST['hyphenator'] ) && !empty( $_POST['hyphenator'] ) )
	{
		if( isset( $_POST['directory'] ) && !empty( $_POST['directory'] ) &&
			isset( $_POST['identifier'] ) && !empty( $_POST['identifier'] ) &&
			isset( $_POST['file'] ) && !empty( $_POST['file'] ) &&
			isset( $_POST['output'] ) && !empty( $_POST['output'] ))
		{
			hyphenator::replace_text( $_POST['directory'], $_POST['identifier'], $_POST['file'], $_POST['output'] );
		}
	}

	if ( isset( $_POST['save_hyphenator'] ) && !empty( $_POST['save_hyphenator'] ) )
	{
		if( isset( $_POST['save_options'] ) && !empty( $_POST['save_options'] ) &&
			isset( $_POST['file_name'] ) && !empty( $_POST['file_name'] ))
		{
			hyphenator::save_values( $_POST['save_options'], $_POST['file_name'] );
		}
	}

	if ( isset( $_POST['delete_JSON'] ) && !empty( $_POST['delete_JSON'] ) )
	{
		if( isset( $_POST['file_name'] ) && !empty( $_POST['file_name'] ))
		{
			hyphenator::delete_saved_value( $_POST['file_name'] );
		}
	}

	class hyphenator
	{
		public static function replace_text($directory, $identifier, $file, $text)
		{
			$directory_items = scandir( dirname(__DIR__) . '/projects/' . $directory );
			$ignore = ['.', '..', '.DS_Store', '_output', 'config.php'];

			foreach ( $directory_items as $item )
			{

				if ( in_array( $item, $ignore ) )
				{
					continue;
				}
				if ( is_dir( __DIR__ . '/../projects/' . $directory . '/' . $item ) )
				{
					hyphenator::replace_text( $directory . '/' . $item, $identifier, $file, $text );
				}
				else
				{
					if ( $file === 'JS' && pathinfo( $item )['extension'] === 'js' )
					{
						if ( file_exists( __DIR__ . '/../projects/' . $directory . '/' . $item) )
						{
							$content = file_get_contents( __DIR__ . '/../projects/' . $directory . '/' . $item );
							$cache = preg_replace( '/(' . $identifier . '.*?\').*?(\';)/', '$1' . $text . '$2', $content );
							file_put_contents( __DIR__ . '/../projects/' . $directory . '/' . $item, $cache );
						}

					}
					if ( $file === 'HTML' && pathinfo($item)['extension'] === 'php' )
					{
						if ( file_exists( __DIR__ . '/../projects/' . $directory . '/' . $item) )
						{
							$content = file_get_contents( __DIR__ . '/../projects/' . $directory . '/' . $item );
							$cache = preg_replace('/(<.*?' . $identifier . '\b.*?>)(?:(?=\s+)\s+|).*?(?:(?=\s+)\s+|)(<\/.*?>)/', '$1' . $text . '$2', $content );
							file_put_contents( __DIR__ . '/../projects/' . $directory . '/' . $item, $cache );
						}
					}
				}
			}
		}

		public static function save_values($data, $name)
		{
			if ( file_exists( 'hyphenator-values' ) === false )
			{
				mkdir( 'hyphenator-values' );
			}

			if ( file_exists( 'hyphenator-values/' . $name . '.json' ) )
			{
				unlink( 'hyphenator-values/' . $name . '.json' );
			}

			$file = fopen( 'hyphenator-values/' . $name . '.json', 'w+');
			fwrite($file, json_encode(json_decode($data), JSON_PRETTY_PRINT));
			fclose($file);

		}

		public static function delete_saved_value($name)
		{
			if ( file_exists( 'hyphenator-values/' . $name ) )
			{
				unlink( 'hyphenator-values/' . $name );
			}
		}
	}

	require __DIR__ . '/../vendor/autoload.php';

	$app_scss_content = file_get_contents( __DIR__ . '/../scss/main.scss' );

	$scss = new Leafo\ScssPhp\Compiler();
	$scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
	$scss->setImportPaths( ['../scss/', '../scss/abstracts', '../scss/base', '../scss/components', '../scss/layout', '../scss/pages', '../scss/vendors'] );
	$app_scss_content = $scss->compile( $app_scss_content );

	$autoprefixer = new Autoprefixer('last 3 version');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>hyphenator.js</title>
		<link rel="shortcut icon" href="assets/favicon.ico">
		<script src="../js/jquery-3.3.1.min.js"></script>
		<script src="../js/hyphenator/Hyphenator.js"></script>
		<?php
			foreach (glob(__DIR__ . '/../js/hyphenator/patterns/*') as $pattern)
			{
				print '<script src="../js/hyphenator/patterns/' . basename($pattern) . '" type="text/javascript"></script>';
			}
		?>
		<style media="screen"> <?php print $autoprefixer->compile($app_scss_content); ?> </style>
	</head>
	<body>

		<div class="hyphenator">
				<div class="clearfix">
					<span id="delete-saved-value"></span>
					<span class="float-right text text-dark">Saved values:
						<select name="saved-values">
							<option value="">choose file</option>
							<?php
								foreach (glob(__DIR__ . '/hyphenator-values/*') as $pattern)
								{
									print '<option value="' . basename($pattern) . '">' . basename($pattern) . '</option>';
								}
							?>
						</select>
					</span>
				</div>

			<form id="form">
				<div class="source">
					<label class="text text-dark">Input
						<textarea name="source" rows="8" placeholder="text.." required></textarea>
						<div id="test"></div>
					</label>
				</div>

				<div class="options clearfix">

					<fieldset class="text text-dark">
						<span>Replace in:</span>

						<input type="radio" id="radio-HTML" name="replace" value="HTML">
						<label for="html"> HTML</label>

						<input type="radio" id="radio-JS" name="replace" value="JS">
						<label for="js"> JS</label>

						<input type="radio" id="radio-input" name="replace" value="input">
						<label for="input"> Input</label>


						<label class="input-char">Hyphenchar
							<input name="char" type="text" placeholder="default: |"/>
						</label>

						<label class="input-text">Identifier
							<input name="identifier" type="text" placeholder="can be a tagName, className, id or js var"/>
						</label>

						<label class="input-text">Directory
							<input name="directory" type="text" placeholder="project/.."/>
						</label>

						<span class="float-right">Language:
							<select name="language" required>
								<option value="">choose file</option>
								<?php
									foreach (glob(__DIR__ . '/../js/hyphenator/patterns/*') as $pattern)
									{
										print '<option value="' . basename($pattern) . '">' . basename($pattern) . '</option>';
									}
								?>
							</select>
						</span>
					</fieldset>
				</div>

				<div class="exeptions">
					<label class="text text-dark">Exeptions
						<textarea name="exeptions" rows="2" placeholder="exeption, exeption, exeption.."></textarea>
					</label>
				</div>

				<button type="submit" class="button button-bright hyphenator-button button-center text text-dark text-regular text-size-normal">Hyphenat it!</button>
			</form>

			<button id="saved-values-button" class="button button-bright hyphenator-button button-small text text-dark text-regular text-size-normal">Save values</button>
		</div>

		<script type="text/javascript">
			$(document).ready(function() {

				function escapeRegExp(str) {
					return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
				}

				$('form textarea[name=source], form select[name=language]').on('invalid', function() {
					this.setCustomValidity('');

			    	if (!this.validity.valid && $(this).attr('name') === 'source') {
			    		this.setCustomValidity('Please enter some text.');
			    	}

			    	if (!this.validity.valid && $(this).attr('name') === 'language') {
			    		this.setCustomValidity('Please choose a language.');
			    	}
				});

				$('form input[type=radio]').on('change', function() {

					if ($(this).val() === 'HTML' || $(this).val() === 'JS') {
						$('form input[type=text]').each(function(key, value) {
							if (!$(this).parent().hasClass('input-char')) {
								$(this)[0].required = true;
							}
						});
					} else {
						$('form input[type=text]').each(function(key, value) {
							if (!$(this).parent().hasClass('input-char')) {
								$(this)[0].required = false;
							}
						});
					}

					$('form input[type=text]').on('invalid', function() {
						this.setCustomValidity('');

			    		if (!this.validity.valid && $(this).attr('name') === 'identifier') {
				    		this.setCustomValidity('Please enter a identifier.');
				    	}

				    	if (!this.validity.valid && $(this).attr('name') === 'directory') {
				    		this.setCustomValidity('Please enter a directory.');
				    	}
					});
				});

				$('#form').on('submit', function(event) {

					var source = $('textarea[name=source]').val(),
						replace = $('input[name=replace]:checked').val(),
						char = $('input[name=char]').val(),
						identifier = $('input[name=identifier]').val(),
						directory = $('input[name=directory]').val(),
						language = $('select[name=language]').val(),
						exeptions = $('textarea[name=exeptions]').val().replace(/\s+/g, '').split(',');

					if (replace === undefined) {
						replace = 'input';
					}

					if (char === undefined || char === '') {
						char = '|';
					}

					if (replace === 'input') {
						event.preventDefault();
					}

					$.each( exeptions, function(key, val) {
						Hyphenator.addExceptions( language.replace(/\..*/, ''), val );
					});

					Hyphenator.config({
						hyphenchar : char
					});

					var pattern = new RegExp(escapeRegExp(char), 'g'),
						output = Hyphenator.hyphenate(source, language.replace(/\..*/, '')).replace(/\|/g, '&shy;');

					if (replace === 'input') {
						$('textarea[name=source]').val(output);
						return false;
					} else {
						$.ajax({
							type: 'post',
							url: 'hyphenator.php',
							data: {
								hyphenator: 'replace_text',
								directory: directory,
								identifier: identifier,
								file: replace,
								output: output
							},
							success: function(response) {
								$('textarea').val('');
								$('input').val('');
								$('select').val('');
								location.reload(true);
				            }
						});
						return false;
					}

				});

				$('#saved-values-button').on('click', function() {

					var saveOptions = {};
						saveOptions.source = $('textarea[name=source]').val(),
						saveOptions.replace = $('input[name=replace]:checked').val(),
						saveOptions.char = $('input[name=char]').val(),
						saveOptions.identifier = $('input[name=identifier]').val(),
						saveOptions.directory = $('input[name=directory]').val(),
						saveOptions.language = $('select[name=language]').val(),
						saveOptions.exeptions = $('textarea[name=exeptions]').val().replace(/\s+/g, '').split(',');

					var fileName = prompt('Please enter a file name.', '');
				    if (fileName !== null || fileName !== '') {
				        $.ajax({
							type: 'post',
							url: 'hyphenator.php',
							data: {
								save_hyphenator: 'save_values',
								save_options: JSON.stringify(saveOptions),
								file_name: fileName
							}
						});
				    }
				});

				$('#delete-saved-value').on('click', function() {
					if ($('select[name=saved-values]').val() !== '') {
						var deleteJSON = $('select[name=saved-values]').val();
						$.ajax({
							type: 'post',
							url: 'hyphenator.php',
							data: {
								delete_JSON: 'delete_saved_value',
								file_name: deleteJSON,
							},
							success: function() {
								$('option[value="' + deleteJSON + '"]').remove();

								$('textarea[name=source]').val('');
								$('input[name=replace]').val('');
								$('input[name=char]').val('');
								$('input[name=identifier]').val('');
								$('input[name=directory]').val('');
								$('select[name=language]').val('');
								$('textarea[name=exeptions]').val('');
							}
						});
					}
				});

				$('select[name=saved-values]').on('change', function() {
					if ($(this).val() !== '') {
						$.getJSON( 'hyphenator-values/' + $(this).val(), function( data ) {
							$('textarea[name=source]').val(data.source);
							$('input[id=radio-' + data.replace + ']').prop("checked", "true");
							$('input[name=char]').val(data.char);
							$('input[name=identifier]').val(data.identifier);
							$('input[name=directory]').val(data.directory);
							$('select[name=language]').val(data.language);
							$('textarea[name=exeptions]').val(data.exeptions.join(','));
						});
					} else {
						$('textarea[name=source]').val('');
						$('input[name=replace]').val('');
						$('input[name=char]').val('');
						$('input[name=identifier]').val('');
						$('input[name=directory]').val('');
						$('select[name=language]').val('');
						$('textarea[name=exeptions]').val('');
					}
				});
			});
		</script>
	</body>
</html>
