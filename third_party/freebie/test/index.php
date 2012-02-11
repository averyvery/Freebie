<!DOCTYPE HTML>
<html>

	<head>
		
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Freebie Tests</title>
		
		<style type="text/css" media="screen">
			body {
				padding: 10px 15px 15px;
				font-family: Helvetica, Arial, sans-serif;
				font-size: 14px;
				line-height: 20px;
			}  
			h3 {
				padding-top: 15px;
			}
			.settings {
				overflow: hidden;
				margin-bottom: 10px;
			}
			.setting {
				border-top: 2px solid #eee;
				padding: 5px 0;
			}
			input[type=text] {
				width: 300px;
			}
			#results {
				border-bottom: 3px solid #ccc;
				padding-bottom: 30px;
				margin-bottom: 30px;
			}
			.success {
				color: green;
			}
			.failure {
				color: red;
			}
		</style>
		
	</head>
	
	<body>
		
		<h2>Test</h2>
		
		<p>Enter the address of your site here, and hit "test."</p>
		
		<input id="site_url" value="http://freebie.local/" type="text"/>
		<input id="test" value="Test!" type="submit" />		 
		
		<ul id="results"></ul>
				
		<h2>Instructions:</h2>
		
		<h3>Settings</h3>
		
		<p>Put the following text into your Freebie settings fields:</p>
		
		<div class="settings">
			<div class="setting">
				<p><strong>Freebie segments:</strong>
					one|two|three|four|f*|s*x|*ven|parse</p>
			</div>
			<div class="setting">
				<p><strong>Break segments:</strong>
					break</p>
			</div>
			<div class="setting">
				<p><strong>Break on category URL indicator:</strong> Yes</p>
			</div>
			<div class="setting">
				<p><strong>Ignore numeric segments:</strong> Yes</p>				
			</div>
			<div class="setting">
				<p><strong>Always parse:</strong> parse</p>				 
			</div>
		</div>

		<h3>Categories</h3>

		<p>Create categories with urls of "one" and "two", names of "One" and "Two", descriptions of "Test", and images of "Image". The IDs will need to be 1 and 2.
		
		<h3>Templates</h3>
		
		<p>Empty the index template; put only the '_freebie_index' into it.</p>
		
		<p>Add the <a href="_freebie_test.group">test template group</a> to EE.</p>
		
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>

		<script type="text/javascript">
			google.load("jquery", "1.4.2");
		</script>
		
		<script type="text/javascript">

			var urls = {
				
				// check the templates
				'_freebie_test' : '_freebie_test',
				'_freebie_test/page' : '_freebie_page',

				// make sure we don't eff with action URLs
				'?ACT=33&id=149&return=/trusted-installers/search-results/London/25/1-2-3-5' : 'action you have requested',
				'?ACT=10&id=2222' : 'Invalid',
				
				
				// basics - putting a freebie segment before/after a url
				'one/_freebie_test' : '_freebie_test',
				'_freebie_test/two' : '_freebie_test',
				'one/_freebie_test/two' : '_freebie_test',
				
				// basics - mixing freebies in with two real segments
				'_freebie_test/page' : '_freebie_page',
				'one/_freebie_test/page' : '_freebie_page',
				'_freebie_test/two/page' : '_freebie_page',
				'one/_freebie_test/two/page' : '_freebie_page',
				'_freebie_test/page/two/' : '_freebie_page',
				'one/_freebie_test/two/page/three' : '_freebie_page',
								
				// simple wildcard tests
				'five/_freebie_test' : '_freebie_test',
				'fiiiiive/_freebie_test' : '_freebie_test',
				'six/_freebie_test' : '_freebie_test',
				'sixxxxxx/_freebie_test' : '_freebie_test',
				'seven/_freebie_test' : '_freebie_test',
				'even/_freebie_test' : '_freebie_test',

				// tricky wildcard tests
				'four/five/_freebie_test/four/five/page' : '_freebie_page',
				'_freebie_test/page/fiiiive/' : '_freebie_page',				
				
				// breaking segment text
				'_freebie_test/break/sandwiches/explosions' : '_freebie_test',
				'_freebie_test/break/page/sandwiches/explosions' : '_freebie_test',
				'_freebie_test/page/break/sandwiches/explosions' : '_freebie_page',
				'_freebie_test/post_break/break/one/two/three' : 'one|two|three|',
								
				// category test
				'_freebie_test/category' : '_freebie_test',
				'_freebie_test/category/page' : '_freebie_test',
				'_freebie_test/category/sandwich/nonsense' : '_freebie_test',				 
								
				// simple numeric test
				'_freebie_test/888' : '_freebie_test',
				'888/_freebie_test' : '_freebie_test',				
				'888/_freebie_test/888' : '_freebie_test',				
				'_freebie_test/888/page' : '_freebie_page',
				'_freebie_test/page/888' : '_freebie_page',				 
				
				// testing freebie:any
				'_freebie_test/any/' : 'false',
				'_freebie_test/any/one/' : 'true',
				'_freebie_test/any/two/one/' : 'true',

				// testing blank freebie tags
				'_freebie_test/tags/one/' : '',
				'_freebie_test/tags/one/two/' : 'two',

				// URLs with extra parameters
				'_freebie_test/search&keywords=foo+bar' : '_freebie_testsearch',
				'_freebie_test/search&keywords=foo+bar&sandwiches=false' : '_freebie_testsearch',
				'_freebie_test/search/?keywords=foo+bar&rice' : '_freebie_testsearch',
				'_freebie_test/search?keywords=foo+bar&rice&sandwiches=false' : '_freebie_testsearch',
				'_freebie_test/search/&keywords=foo+bar&rice&sandwiches=false' : '_freebie_testsearch',
				'_freebie_test/search/&keywords=foo+bar&rice&sandwiches=false&start=0/P3' : '_freebie_testsearch',
				'_freebie_test/search/&keywords=foo+bar&rice&sandwiches=false&start=0/P3' : '_freebie_testsearch',
				
				// all together now
				'one/siiiiix/_freebie_test/page/two/break/sandwiches/explosions' : '_freebie_page',

				// check if number
				'_freebie_test/numbers/88/' : 'false/true',
				'_freebie_test/numbers/88ninety/' : 'false/false',
				'_freebie_test/numbers/any/' : 'false/false',

				// get category ids from strings
				'_freebie_test/categories/one/' : '1|One|One|Test||Image|One|Test',
				'_freebie_test/categories/two/' : '2|Two|Two|Test||Image|Two|Test',
				'_freebie_test/categories/three/' : '',
				
				// always_parse
				'_freebie_test/parse/' : 'Parsed!',
				
				// debug
				'_freebie_test/last/one/two/three/four' : 'four',

				// debug
				'_freebie_test/debug/' : 'To ignore: one|two|three|four|f.*?|s.*?x|.*?ven|parse',
				'_freebie_test/debug/' : 'Ignore beyond: break',
				'_freebie_test/debug/' : 'Break category: yes',
				'_freebie_test/debug/' : 'Remove numbers: yes',
				'_freebie_test/debug/' : 'Always parse: parse',
				'_freebie_test/debug/' : 'URI: _freebie_test/debug',
				'_freebie_test/debug/' : 'Segments: _freebie_test+debug',
				'_freebie_test/debug/' : 'URI cleaned: _freebie_test/debug'
				
			}
			
			function output ( type, url, html ) {

				var 
					feedback, 
					feedback_class, 
					feedback_start, 
					feedback_end,
					html = html.replace(/\n/g, ''), 
					text_found = html.indexOf( urls[ url ] ) != -1
				
				if ( text_found ) {
					feedback = 'successful!';
					feedback_class = 'success';
				} else if ( type == 'success' ){
					feedback = 'failed! Output: ' + html;					 
					feedback_class = 'failure';
				} else {
					feedback = 'caused error!' + html;										
					feedback_class = 'failure';
				}
				feedback_start = '<li class=' + feedback_class +'><strong>' 
													+ url + '</strong> ';
				feedback_end = '</li>';
				feedback = '<p>' + feedback_start + feedback + feedback_end + '</p>';
				$('#results').append(feedback);
			}
			
			function test_urls ( root ){
				
				for ( url in urls ){

					// self-executing function to freeze vars
					(function(url){
					
						$.ajax({
							type: 'POST',
							data: { 'url' : root + url },
							url: 'proxy.php',
							success: function( html, test, request ){
								output( 'success', url, html );							 
							},
							error: function( request, status, html ){
								output( 'error', url, html );
							}
						})
						
					})(url);
					
				}
				
			}
			
			$('#test').click(function(){
				var root = $('#site_url').val();
				$('#results').html('');
				test_urls( root );
			});

		</script>
		
	</body>
	
</html>
