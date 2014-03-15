<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Хуиза</title>
<link rel="stylesheet" type="text/css" href="/devel/mona/1.css">
</head>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/annyang/0.2.0/annyang.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script type="text/javascript">
if (annyang) {
  // Let's define our first command. First the text we expect, and then the function it should call
  var commands = {
    'alert': function() {
      alert('Пеший дно');
    }
  };

  // Initialize annyang with our commands
  annyang.init(commands);

  // Start listening. You can call this here, or attach this call to an event, button, etc.
  annyang.start();
}
</script>
<body>
<div id="monalisa"></div>


</body>
</html>