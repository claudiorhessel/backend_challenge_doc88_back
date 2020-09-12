<!doctype html>
<html>
<head>
   @include('includes.headMail')
</head>
<body>
<div class="container">
   <header class="row">
       @include('includes.headerMail')
   </header>
   <div id="main" class="row">
           @yield('content')
   </div>
   <br />
   <footer class="row">
       @include('includes.footerMail')
   </footer>
</div>
</body>
</html>
