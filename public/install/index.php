<?php require "helper.php";?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
 
    <title>CRUDBooster :: Installation</title>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        .container {
          font-size: 12px
        }
        #footer {
          font-size: 11px;
          text-align: center;
        }
    </style>
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
  </head>

  <body>
    <div class="container" style='max-width: 600px;margin-bottom: 30px'>

      <div class="page-header">
        <h3><i class='ion ion-android-download'></i> CRUDBooster Setup Wizard</h3>
      </div>




      <div class='step' id='step1'>
        
      <h4>Step 1: Server Requirements</h4>
      <p class='text-muted'>We make sure your server are compatible with CRUDBooster</p>
      <table class='table table-striped table-bordered'>
          <thead>
              <tr>
                  <th width="70%">Component</th>
                  <th>Status</th>
              </tr>
          </thead>
          <tbody>
              <?php                   
                  $failed = 0;
              ?>
              <tr>
                  <td>PHP Version</td>
                  <td>
                  <?php 
                  if(phpversion()>=5.4) {
                    echo "<span class='label label-success'>".phpversion()."</span>";
                  }else{
                    $failed += 1;
                    echo "<span class='label label-danger'>".phpversion()."</span>";
                  }
                  ?>
                  </td>
              </tr>

              <tr>
                  <td>Mcrypt Extension</td>
                  <td>
                    <?php 
                      if(function_exists("mcrypt_encrypt")) {
                          echo "<span class='label label-success'>GOOD</span>";
                      } else {
                          $failed += 1;
                          echo "<span class='label label-danger'>BAD</span>";
                      }
                    ?>
                  </td>
              </tr>

              <tr>
                  <td>OpenSSL Extension</td>
                  <td>
                    <?php 
                      if(extension_loaded('openssl')) {
                          echo "<span class='label label-success'>GOOD</span>";
                      } else {
                          $failed += 1;
                          echo "<span class='label label-danger'>BAD</span>";
                      }
                    ?>
                  </td>
              </tr>

              <tr>
                  <td>MBString Extension</td>
                  <td>
                    <?php 
                      if(extension_loaded('mbstring')) {
                          echo "<span class='label label-success'>GOOD</span>";
                      } else {
                          $failed += 1;
                          echo "<span class='label label-danger'>BAD</span>";
                      }
                    ?>
                  </td>
              </tr>

              <tr>
                  <td>Tokenizer Extension</td>
                  <td>
                    <?php 
                      if(extension_loaded('tokenizer')) {
                          echo "<span class='label label-success'>GOOD</span>";
                      } else {
                          $failed += 1;
                          echo "<span class='label label-danger'>BAD</span>";
                      }
                    ?>
                  </td>
              </tr>          


          </tbody>
      </table>
      <div class='alert alert-info'>
        <strong>WARNING: </strong> Please Make sure all the requirements become passed
      </div>

      <div class='alert alert-info'>
         <strong>Attention</strong><br/>
         Please make sure you have made an empty database. If you have not created database, please create one before going to the next step
      </div>
      <a class='btn btn-block btn-default <?php echo ($failed>0)?"disabled":""?>' href='javascript:void(0)' onclick="next_step(2)">Continue Next Step &raquo;</a>

      </div><!--END STEP1-->

      <div class='step' style='display: none' id='step2'>
          <h4>Step 2: Configuration Database</h4>
          <p class='text-muted'>We make sure your server are compatible with CRUDBooster</p>

          <?php             
            $conf = load_env();
          ?>

          <form method='post' id='form-db' action='./execute.php?type=save_db'>
            <table class='table table-striped table-bordered'>
            <tbody>
              <tr><td><strong>Hostname</strong></td><td><input required type='text' class='form-control' value='<?php echo @$conf['DB_HOST']?>' name='DB_HOST'/></td></tr>              
              <tr><td><strong>Username</strong></td><td><input required type='text' class='form-control' value='<?php echo @$conf['DB_USERNAME']?>' name='DB_USERNAME'/></td></tr>
              <tr><td><strong>Password</strong></td><td><input type='text' class='form-control' value='<?php echo @$conf['DB_PASSWORD']?>' name='DB_PASSWORD'/></td></tr>
              <tr><td><strong>Database</strong></td><td><input required type='text' class='form-control' value='<?php echo @$conf['DB_DATABASE']?>' name='DB_DATABASE'/></td></tr>
            </tbody>
            </table>
            <input type='submit' class='btn btn-block btn-default' value='Save & Continue'/>
          </form>

          <script type="text/javascript">
            $(function() {
                $("#form-db").submit(function() {
                  $.ajax({
                    type:'POST',
                    url:$(this).attr('action'),
                    data:$(this).serialize(),
                    success:function(r) {
                      if(r==1) {
                        next_step(3);
                      }else{
                        alert("Database Can`t Created !");
                      }
                    },
                    error:function() {
                      alert('Request faield !');
                    }
                  })
                  return false;
                })
            })
          </script>
      </div>


      <div class='step' style='display: none' id='step3'>
          <h4>Step 3: Ready to Installing / Updating</h4>
          <p class='text-muted'>We make sure your server are compatible with CRUDBooster</p>

          <table class='table table-striped table-bordered'>
              <thead><th>Table Name</th><th>Exists</th></thead>
              <tbody>
                  <?php                       
                      $queries = SplitSQL("./database.sql");          
                      foreach($queries['tables'] as $row):                      
                  ?>
                  <tr>
                    <td><?php echo $row?></td>
                    <td class='status_table' data-table='<?php echo $row?>'>Waiting for action...</td>
                  </tr>
                  <?php endforeach;?>
              </tbody>
          </table>
          <script type="text/javascript">
            check_table();
            function check_table() {
              $(".status_table").each(function() {
                var table = $(this).attr('data-table');
                var h = $(this);
                $.post("./execute.php?type=check_table",{table:table},function(resp) {
                    if(resp==1) {
                      h.html("<span class='label label-success'>ALREADY</span>");
                    }else{
                      h.html("<span class='label label-default'>NEED</span>");
                    }
                })
              })
            }
          </script>

          <a class='btn btn-block btn-success' href='javascript:void(0)' onclick="install()">Procceed To Install/Update &raquo;</a>

          <script type="text/javascript">
            function install() {
              var url = "../updater";
              window.open(url,"iframe_install");
              next_step(4);
              $("#iframe_install").load(function() {
                 alert("Install Finished !. Please rename folder `install` for security reason.");
                 $("#step4 h4").html("Step 4: <span class='text-success'>Install Finished !</span>");
                 //$("#step4 iframe").slideUp();
                 $("#step4").append("<br/><div align='center'><a href='../' title='click to go view the web'>Click here to View Web</a></div>");
              })
            }
          </script>
      </div>


      <div class='step' style="display: none" id='step4'>
          <h4>Step 4: Installing...</h4>
          <p class='text-muted'>Please wait until instalation is finished...</p>
          <iframe style='width:100%;border:1px solid #dddddd;height: 500px' id='iframe_install' name='iframe_install'></iframe>
      </div>

    </div> <!-- /container -->

      <script>
      function next_step(n) {
        $(".step").slideUp();
        $("#step"+n).slideDown();
      }
      </script>

      <div align="center" id='footer'>Powered by <a href='http://crudbooster.com'>CRUDBooster</a></div>
  </body>
</html>
