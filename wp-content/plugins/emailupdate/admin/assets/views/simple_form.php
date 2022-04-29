<!DOCTYPE html>
<html lang="en">

  <head>
    <title>Change font</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script> -->
    <style>
      #change_font_form label.error{color: red;}
    </style>
  </head>
  
  <body>
    <div class="container">
      <!-- <h2>Change Email here</h2> -->
      <br/>
      <form class="form-horizontal" id="simple_form" action="#">
        <div class="form-group">
          
          <label class="control-label col-sm-2" for="emailinput">Email Address:</label>
          <div class="col-sm-10" style="margin-bottom: 5px;">
            <input type="email" class="form-control" id="emailinput" placeholder="Enter your new email address" name="emailinput">
          </div>

          <br/>
          
          <label class="control-label col-sm-2" for="emailconfirm" style="white-space: nowrap;">Confirm Email Address:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="emailconfirm" placeholder="Please confirm your new email address" name="emailconfirm">
          </div>
          
        </div>

        <br/>

        <div class="form-group">        
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default" id="register" disabled="disabled">Submit</button>&nbsp;
          </div>
        </div>
      </form>
    </div>
  </body>

</html>