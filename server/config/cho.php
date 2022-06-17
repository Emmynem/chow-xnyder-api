<?php

  // date_default_timezone_set("Africa/Lagos");

  // //   ---- My Codes ----
  // $start_time = date("Y-m-d H:i:s");
  // echo $start_time;

  // for ($i = 0; $i < 20; $i++){
  //     echo $i." who goes there";
  // }

  // //   ---- My Codes ----
  // $end_time = date("Y-m-d H:i:s");
  // echo $end_time;

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;

  require '../phpmailer/src/Exception.php';
  require '../phpmailer/src/PHPMailer.php';
  require '../phpmailer/src/SMTP.php';

  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);
  function initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage){
      global $mail;

      try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->SMTPDebug = false;                      // Enable verbose debug output
        $mail->do_debug = 2;                         //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $mailHost;                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $mailUsername;                     //SMTP username
        $mail->Password   = $mailPassword;                               //SMTP password
        $mail->SMTPSecure = 'tls';             //Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
          );
        //Recipients
        $mail->setFrom($mailSender, $mailSenderTitle);
        $mail->addAddress($mailRecipient, $mailRecipientTitle);     //Add a recipient
        // $mail->addAddress('ellen@example.com');               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mailSubject;
        $mail->Body    = $mailMessage;
        $mail->AltBody = strip_tags($mailMessage);

        $mail->send();
        // echo 'Message has been sent';
      } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
    }

  $headerPart = '
    <!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
      <title>Pryur Email</title>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
      <meta http-equiv="Content-Security-Policy" content="child-src *;">
      <meta name="author"  content="Pryur"/>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Jost&display=swap" rel="stylesheet">
      <style>
        *{
          margin:0;
          padding:0;
          box-sizing:border-box;
        }
        body::-webkit-scrollbar{
          width:8px;
        }
        body::-webkit-scrollbar-track{
          background-color:#fff;
        }
        body::-webkit-scrollbar-thumb{
          background-color:#2852dc;
        }
        h1{
          color:#2d2c2f;
          font-size:5em;
          font-family:"Jost", apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        h2{
          color:#2d2c2f;
          font-size:3.5em;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        h3{
          color:#2d2c2f;
          font-size:2.5em;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        h4{
          color:#2d2c2f;
          font-size:1.5em;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        h6{
          color:#2d2c2f;
          font-size:1.2em;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        p{
          color:#203f58;
          font-size:1em;
          line-height:35px;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        a{
          color:#2852dc !important;
        }
        .pry-txt.left{
          text-align:left;
        }
        .pry-txt.center{
          text-align:center;
        }
        .pry-txt.right{
          text-align:right;
        }
        .pry-txt.justify{
          text-align:justify;
        }
        .pry-txt.no-decoration{
          text-decoration:none;
        }
        .pry-overlay{
          width:100%;
          height:100%;
          background-color:rgb(0,0,0,0.5);
        }
        .pry-container{
          padding:0 5%;
        }
        .pry-container .swiper-pagination span{
          width:30px;
          height:5px;
          outline:0;
          border-radius:0;
          border-color:#fff;
          margin:20px 10px 0 0;
        }
        .pry-container .swiper-pagination-bullet-active{
          background-color:#2852dc;
        }
        .pry-box-shadow{
          box-shadow: 1px 1px 5px 1px #e3e3e3;
        }
        .pry-spacer{
          display:block;
          height:80px;
        }
        .pry-inner-space{
          padding:30px 25px;
        }
        .pry-position{
          position:static;
        }
        .pry-position.absolute{
          position:absolute;
        }
        .pry-position.sticky{
          position:sticky;
        }
        .pry-position.relative{
          position:relative;
        }
        .pry-position.fixed{
          position:fixed;
        }
        .pry-flex{
          display:flex;
          flex-wrap:wrap;
        }
        .pry-flex.direction-row-reverse{
          flex-direction:row-reverse;
        }
        .pry-flex.direction-column{
          flex-direction:column;
        }
        .pry-flex.direction-column-reverse{
          flex-direction:column-reverse;
        }
        .pry-flex.inline{
          display:inline-flex;
        }
        .pry-flex-align-items{
          align-items:flex-start;
        }
        .pry-flex-align-items.end{
          align-items:flex-end;
        }
        .pry-flex-align-items.center{
          align-items:center;
        }
        .pry-flex-align-items.stretch{
          align-items:stretch;
        }
        .pry-flex-align-items.baseline{
          align-items:baseline;
        }
        .pry-flex-justify-content{
          justify-content:flex-start;
        }
        .pry-flex-justify-content.center{
          justify-content:center;
        }
        .pry-flex-justify-content.end{
          justify-content:flex-end;
        }
        .pry-flex-justify-content.around{
          justify-content:space-around;
        }
        .pry-flex-justify-content.between{
          justify-content:space-between;
        }
        .pry-grid{
          display:grid;
          grid-gap:30px;
        }
        .pry-grid.no-gap{
          grid-gap:0;
        }
        .pry-grid.two{
          grid-template-columns:repeat(2,minmax(0,1fr));
        }
        .pry-grid.three{
          grid-template-columns:repeat(3,minmax(0,1fr));
        }
        .pry-grid.four{
          grid-template-columns:repeat(4,minmax(0,1fr));
        }
        .pry-grid.five{
          grid-template-columns:repeat(5,minmax(0,1fr));
        }
        .pry-grid.six{
          grid-template-columns:repeat(5,minmax(0,1fr));
        }
        .pry-grid.two.no-stack{
          grid-template-columns:repeat(2,minmax(0,1fr));
        }
        .pry-grid.three.no-stack{
          grid-template-columns:repeat(3,minmax(0,1fr));
        }
        .pry-fg-main{
          color:#2852dc;
        }
        .pry-fg-grey{
          color:#555;
        }
        .pry-fg-white{
          color:#fff;
        }
        .pry-fg-green{
          color:#1cd3a2;
        }
        .pry-fg-red{
          color:#e60626;
        }
        .pry-fg-black{
          color:#2d2c2f;
        }
        .pry-fg-main90{
          color:#dce4f9;
        }
        .pry-fg-green90{
          color:#e5fdff;
        }
        .pry-fg-red90{
          color:#fecdd5;
        }
        .pry-fg-main95{
          color:#f2f5fd !important;
        }
        .pry-fg-green95{
          color:#f5feff;
        }
        .pry-fg-red95{
          color:#fee6ea;
        }
        .pry-bg-main{
          background-color:#2852dc;
        }
        .pry-bg-grey{
          background-color:#555;
        }
        .pry-bg-white{
          background-color:#fff;
        }
        .pry-bg-green{
          background-color:#1cd3a2;
        }
        .pry-bg-red{
          background-color:#e60626;
        }
        .pry-bg-black{
          background-color:#2d2c2f;
        }
        .pry-bg-main90{
          background-color:#dce4f9;
        }
        .pry-bg-green90{
          background-color:#e5fdff;
        }
        .pry-bg-red90{
          background-color:#fecdd5;
        }
        .pry-bg-main95{
          background-color:#f2f5fd;
        }
        .pry-bg-green95{
          background-color:#f5feff;
        }
        .pry-bg-red95{
          background-color:#fee6ea;
        }
        .pry-btn{
          outline:0;
          font-size:1em;
          padding:13px 20px;
          border:1px solid #fff;
          border-radius:0;
          display:inline-block;
          text-decoration:none;
          font-weight:900;
          font-family:"Jost",apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        .pry-btn.full{
          width:100%;
          padding:15px 25px;
        }
        .pry-hr{
          width:100%;
          border-bottom:1px solid #e5e5e5;
        }
        .pry-mail-container{
          width:100%;
          height:100%;
          top:0;
          left:0;
          padding:50px 0;
          overflow:auto;
        }
        .pry-mail-card{
          width:600px;
          border:1px solid #e9e9e9;
          background-size:cover;
          background-position:center;
          background-image:url("https://app.pryur.com/site/img/white-card-pattern.png");
        }
        .pry-mail-card img{
          object-fit:cover;
          object-position:center;
        }
        .pry-mail-card img.profile{
          width:90px;
          height:90px;
          border-radius:20px;
          object-position:top center;
        }
        .pry-mail-card img.blog{
          width:100%;
          height:500px;
        }
        @media(max-width:900px){
          p{
            line-height:28px;
          }
          .pry-mail-container{
            padding:10px 1%;
          }
          .pry-mail-card{
            width:100%;
          }
        }
      </style>
    </head>
    <body>
    ';
  $footerPart = '
      </body>
    </html>
    ';

  function ceoMail($username,$email){
    global $footerPart,$headerPart;

    $ceoTemplate = '
      <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
        <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
          <img class="profile" src="https://xnyder.com/img/onimisi.jpg" alt="Onimisi">
          <br><br>
          <h4>Howdy, '. $username . '</h4>
          <br>
          <p>
            My name is Ubani Onimisi Alabi and i am the Ceo and Co founder of
            Xnyder the base company of Pryur. I sent you this email because I
            noticed you signed up for one of our tool (Pryur site). It is so
            nice that you could choose our tool(s) over others. I hope you have
            a great experience using our tool and it meets your need. If you have
            problems or difficulties with our tool(s) feel free to always reach
            out to me or the team for help.
          </p>
          <br>
          <h6><a href="https://pryur.co/onimisi.ai" class="pry-fg-main pry-txt no-decoration">Know more of me</a></h6>
          <br>
          <a class="pry-btn pry-fg-main95 pry-bg-main" href="https://instagram.com/onimisi.ai">
            Connect
          </a>
        </div>
      </section>
      ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'onimisi@xnyder.com';
    $mailPassword = 'optimaltech';
    $mailSender = 'onimisi@xnyder.com';
    $mailSenderTitle = 'Welcome to Pryur';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = 'Welcome '.$username;
    $mailMessage = $headerPart.$ceoTemplate.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function freeTrialMail($username,$email){
    global $footerPart,$headerPart;

    $template = '
      <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
        <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
          <img class="profile" src="https://pryur.com/img/favicon.png" alt="Logo">
          <br><br>
          <h4>Hi, ' . $username . '</h4>
          <p class="pry-fg-main" style="padding:5px 0;"><strong>1 month free</strong></p>
          <p>
            There is great news for you. As part of our good deeds we are giving you a month subscription for our premium plan
            so you can enjoy the endless possibilities of <a href="https://pryur.com">Pryur</a>. During this time if you enjoy
            using <a href="https://pryur.com">Pryur</a> you can save towards your next subscription to keep enjoying amazing
            features.
            It will be awesome if you can tell others about <a href="https://pryur.com">Pryur</a>.
          </p>
          <br>
          <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
          <br>
          <a class="pry-btn pry-fg-main95 pry-bg-main" href="https://app.pryur.com/overview">
            Start enjoying
          </a>
        </div>
      </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'no-reply@xnyder.com';
    $mailPassword = 'Rure1821185/';
    $mailSender = 'no-reply@xnyder.com';
    $mailSenderTitle = 'Pryur - One month premium trial';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = $username.' you have a free trial';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function getStartedMail($username,$email){
    global $footerPart,$headerPart;

    $template = '
      <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
      <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
        <img class="profile" src="https://pryur.com/img/favicon.png" alt="Logo">
        <br><br>
        <h4>Yo, '. $username . '</h4>
        <br>
        <p>
          I am <strong>Richard Gigi</strong> Product designer at <a href="https://pryur.com">Pryur</a>.
          I just want to thank you for using our tool (Pryur site), but most importantly i want to walk you through
          the tool to make things much easier. I created an article on getting started with Pryur site which explains
          alot. Check the link below.
        </p>
        <br>
        <h6><a href="https://pryur.com/blog/read/getting-started/1" class="pry-fg-main pry-txt">Go to article</a></h6>
        <br>
        <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
        <br>
        <a class="pry-btn pry-fg-main95 pry-bg-main" href="https://pryur.com/blog">
          Visit our blog
        </a>
      </div>
      </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'gigi@xnyder.com';
    $mailPassword = 'WilberForce2001/';
    $mailSender = 'gigi@xnyder.com';
    $mailSenderTitle = 'Gigi from Pryur';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = 'Get started with Pryur '.$username;
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function failedRenewalMail($username,$email,$amount,$plan){
    global $footerPart,$headerPart;

    $template = '
    <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
    <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
      <img class="profile" src="https://app.pryur.com/site/img/logo.png" alt="Logo">
      <br><br>
      <h4 class="pry-fg-red">Oops renewal failed</h4>
      <br>
      <p>
        Hi <strong>'. $username .'</strong> we tried to deduct <span class="pry-fg-red">'. $amount .'</span>
        from your Pryur site wallet to renew
        your '. $plan .' plan subscription but it was not successful. Due to failed renewal
        you will now be on a free plan and have limited features. To keep enjoying the
        endless possibilities of <span class="pry-fg-main">Pryur</span> you can fund
        your account and renew your payment.
      </p>
      <br>
      <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
      <br>
      <a class="pry-btn pry-fg-main95 pry-bg-black" href="https://app.pryur.com/site/overview">
        Go to account
      </a>
    </div>
    </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'no-reply@xnyder.com';
    $mailPassword = 'Rure1821185/';
    $mailSender = 'no-reply@xnyder.com';
    $mailSenderTitle = 'Pryur renewal';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = $username.' a transaction failed on your account';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function planSubscriptionMail($username,$email,$plan,$amount,$amountPaid,$planStart,$planEnd){
    global $footerPart,$headerPart;

    $template = '
    <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
      <img class="profile" src="https://app.pryur.com/site/img/logo.png" alt="Logo">
      <br><br>
      <h4 class="pry-fg-green">Subscription successful</h4>
      <br>
      <p>
        Dear <strong>' .$username. '</strong> we cannot thank you enough for trusting us.
        Below is the summary of your subscription. View more transactions on
        your dashboard.
      </p>
      <br><br>
      <div class="pry-grid two">
          <div>
            <h6>Plan</h6>
          </div>
          <div class="pry-flex pry-flex-justify-content end">
            <p>'.$plan.'</p>
          </div>
      </div>
      <br><br>
      <div class="pry-hr"></div>
      <br><br>
      <div class="pry-grid two">
          <div>
            <h6>Amount</h6>
          </div>
          <div class="pry-flex pry-flex-justify-content end">
            <p>'.$amount.'</p>
          </div>
      </div>
      <br><br>
      <div class="pry-hr"></div>
      <br><br>
      <div class="pry-grid two">
          <div>
            <h6>Amount paid</h6>
          </div>
          <div class="pry-flex pry-flex-justify-content end">
            <p>'.$amountPaid.'</p>
          </div>
      </div>
      <br><br>
      <div class="pry-hr"></div>
      <br><br>
      <div class="pry-grid two">
          <div>
            <h6>Plan start</h6>
          </div>
          <div class="pry-flex pry-flex-justify-content end">
            <p>'.$planStart.'</p>
          </div>
      </div>
      <br><br>
      <div class="pry-hr"></div>
      <br><br>
      <div class="pry-grid two">
          <div>
            <h6>Plan end</h6>
          </div>
          <div class="pry-flex pry-flex-justify-content end">
            <p>'.$planEnd.'</p>
          </div>
      </div>
      <br><br>
      <p>
        We hope you have a great experience using our tool and get value for your money.
        We are here to make you get the best service you need.
      </p>
      <br>
      <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
    </div>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'no-reply@xnyder.com';
    $mailPassword = 'Rure1821185/';
    $mailSender = 'no-reply@xnyder.com';
    $mailSenderTitle = 'Pryur subscription';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = $username.' your subscription was successful';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function templatePurchaseMail($username,$email){
    global $footerPart,$headerPart;

    $template = '
    <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
      <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
        <img class="profile" src="https://app.pryur.com/site/img/logo.png" alt="Logo">
        <br><br>
        <h4 class="pry-fg-main">Thank you for your purchase</h4>
        <br>
        <p>
          Hi <strong>'. $username .'</strong> thank you for your template purchase on <span class="pry-fg-main">Pryur site</span>.
          Login to your dashboard to see details of the purchase.
        </p>
        <br>
        <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
        <br>
        <a class="pry-btn pry-fg-main95 pry-bg-black" href="https://app.pryur.com/site/overview">
          Go to account
        </a>
      </div>
    </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'no-reply@xnyder.com';
    $mailPassword = 'Rure1821185/';
    $mailSender = 'no-reply@xnyder.com';
    $mailSenderTitle = 'Pryur templates';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = $username.' your purchase was successful';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function planExpirationReminderMail($username,$email,$days){
    global $footerPart,$headerPart;
    if($days == 1){
      $daysLeft = 'a day';
    }else{
      $daysLeft = $days. ' days.';

    }
    $template = '
      <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
        <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
          <img class="profile" src="https://app.pryur.com/site/img/logo.png" alt="Logo">
          <br><br>
          <h4 class="pry-fg-red">Plan expiration</h4>
          <br>
          <p>
            Hi <strong>'. $username. '</strong> your Pryur site subscription ends in
            <span class="pry-fg-red">' .$daysLeft. '</span>. Do not get disconnected from amazing features.
            Fund your account before plan expires to renew subscription.
          </p>
          <br>
          <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
          <br>
          <a class="pry-btn pry-fg-main95 pry-bg-black" href="https://app.pryur.com/site/overview">
            Go to account
          </a>
        </div>
      </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'no-reply@xnyder.com';
    $mailPassword = 'Rure1821185/';
    $mailSender = 'no-reply@xnyder.com';
    $mailSenderTitle = 'Pryur renewal';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = $username.' your plan on Pryur site is about to expire';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  function siteCheckUpMail($username,$email){
    global $footerPart,$headerPart;
    $template = '
      <section class="pry-mail-container pry-position fixed pry-flex pry-flex-align-items center pry-flex-justify-content">
          <div class="pry-mail-card pry-bg-white pry-box-shadow pry-inner-space">
            <img class="profile" src="https://app.pryur.com/site/img/logo.png" alt="Logo">
            <br><br>
            <h4 class="pry-fg-main">Just checking up</h4>
            <br>
            <p>
              Hi <strong>'. $username .'</strong> so i decided to reach out to you today concerning our tool (Pryur site). I really hope it meet your needs
              and you are having a nice time using it. It might be negative for you but we can help if you help us too
              by telling us your issues with our tool. Just send a feedback and we will attend to your issue.
            </p>
            <br>
            <p class="pry-fg-grey"><a href="https://www.youtube.com/channel/UCKbjWcwj4E7Y9m447XnYJHw" class="pry-fg-black pry-txt no-decoration">Youtube</a> | <a href="https://instagram.com/pryurforyou" class="pry-fg-black pry-txt no-decoration">Instagram</a> | <a href="https://twitter.com/xnyderhq" class="pry-fg-black pry-txt no-decoration">Twitter</a> | <a href="https://community.xnyder.com/support" class="pry-fg-black pry-txt no-decoration">Support</a></p>
            <br>
            <a class="pry-btn pry-fg-main95 pry-bg-black" href="https://app.pryur.com/site/settings?call=feedback">
              Send a feedback
            </a>
          </div>
      </section>
    ';

    $mailHost = 'xnyder.com';
    $mailUsername = 'gigi@xnyder.com';
    $mailPassword = 'WilberForce2001/';
    $mailSender = 'gigi@xnyder.com';
    $mailSenderTitle = 'Gigi from Pryur';
    $mailRecipient = $email;
    $mailRecipientTitle = $username;
    $mailSubject = 'Product feedback';
    $mailMessage = $headerPart.$template.$footerPart;
    initiateEmailSend($mailHost,$mailUsername,$mailPassword,$mailSender,$mailSenderTitle,$mailRecipient,$mailRecipientTitle,$mailSubject,$mailMessage);
  }

  $narration = "Plan downgrade";
  $planName = "Pro";

  planSubscriptionMail("Someone Anyone","something@gmail.com",$planName,2300,2300,"2021-12-12","2022-12-12");

  $end_time = date("Y-m-d H:i:s");
  echo $end_time;

?>
