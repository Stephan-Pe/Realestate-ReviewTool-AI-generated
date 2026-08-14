# How to build the HTML5 views for PHP MVC framework

## The base.html example

```html
<!DOCTYPE html>
<html lang="de">

	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta http-equiv="Cache-control" content="public">
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>
		<meta name="format-detection" content="telephone=no"/>
		<meta name="author" content="siteart webagentur"/>

		<link rel="apple-touch-icon" sizes="48x48" href="img/icons/maskable_icon_x48.png">
		<link rel="apple-touch-icon" sizes="72x72" href="img/icons/maskable_icon_x72.png">
		<link rel="apple-touch-icon" sizes="96x96" href="img/icons/maskable_icon_x96.png">
		<link rel="apple-touch-icon" sizes="128x128" href="img/icons/maskable_icon_x128.png">
		<link rel="apple-touch-icon" sizes="192x192" href="img/icons/maskable_icon_x192.png">
		<link rel="apple-touch-icon" sizes="384x384" href="img/icons/maskable_icon_x384.png">
		<link rel="apple-touch-icon" sizes="512x512" href="img/icons/maskable_icon_x512.png">
		<link rel="icon" type="image/png" sizes="192x192" href="img/icons/maskable_icon_x192.png">
		<link rel="icon" type="image/png" sizes="48x48" href="img/icons/maskable_icon_x48.png">
		<link rel="icon" type="image/png" sizes="96x96" href="img/icons/maskable_icon_x96.png">
		<link rel="icon" type="image/png" sizes="128x128" href="img/icons/maskable_icon_x128.png">
		<link
		rel="manifest" href="/manifest.json">
		<!-- Preload the LCP image with a high fetchpriority so it starts loading with the stylesheet. -->
		<link rel="preload" fetchpriority="high" as="image" href="/img/project/chrwld_120kb.webp" type="image/webp">

		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="img/icons/maskable_icon_x72.png">
		<meta name="theme-color" content="#ffffff">
		<meta
		name="google-site-verification" content="zxnVsv5uR1mutplnrJBwiUWnuGcWMWRML_sqHtEp1P8"/>
		<!-- CSS only -->
		<link rel="stylesheet" type="text/css" href="/css/styles.css"/>
		<link rel="canonical" href=" {% block canonical %}{% endblock %}">
		{% block ld_json %} {% endblock %}


			<title>{% block title %}
		{% endblock %}
	</title>
	</head>

		<body>
			<header class="header"> <nav class="nav" role="navigation">
				<div class="nav__offscreen">
					<a class="nav__item" href="/exampleone" aria-label="" title=" " id=" ">Example One</a>
					<a class="nav__item" href="/exampletwo" aria-label=" " title=" " id=" ">Examle Two</a>
			
				</nav>


			</header>

			<!-- loader-container-start -->
		<div class="loader-container">
			<div class="loader"></div>
		</div>
			<!-- loader-container-end -->

		
			{% for message in flash_messages %}
				<div class="alert alert-{{ message.type }}">{{ message.body }}</div>
			{% endfor %}
		

				<div class="container">

					{% block body %}

						<!-- this is where the other views go -->

					{% endblock %}
				</div>
				<button class="totop__btn" aria-lable="Scroll to Top" title="Scroll to Top" id="toTop">
   
				</button>

				<footer class="footer">
                <!-- content goes here -->
				</footer>
				{% block javascripts %}{% endblock %}

				<!-- build:js -->
				<script src="/js/main.min.js"></script>
				<!-- endbuild -->


				{% block footer %}{% endblock %}
			</body>

		</html>
	</body>
</html>

```