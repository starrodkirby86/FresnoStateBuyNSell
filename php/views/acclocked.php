<?php
    function getValidationEmailBody($UserID, $HashToken, $FirstName, $LastName) {
        return "
            <HTML>
            <head>
                <meta name='viewport' content='width=device-width'>
                <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
                <title>Verification Email</title>
                <style type='text/css'>
                  /* -------------------------------------
                            INLINED WITH https://putsmail.com/inliner
                        ------------------------------------- */
                        /* -------------------------------------
                            RESPONSIVE AND MOBILE FRIENDLY STYLES
                        ------------------------------------- */
                        @media only screen and (max-width: 620px) {
                          table[class=body] h1 {
                            font-size: 28px !important;
                            margin-bottom: 10px !important; }
                          table[class=body] p,
                          table[class=body] ul,
                          table[class=body] ol,
                          table[class=body] td,
                          table[class=body] span,
                          table[class=body] a {
                            font-size: 16px !important; }
                          table[class=body] .wrapper,
                          table[class=body] .article {
                            padding: 10px !important; }
                          table[class=body] .content {
                            padding: 0 !important; }
                          table[class=body] .container {
                            padding: 0 !important;
                            width: 100% !important; }
                          table[class=body] .main {
                            border-left-width: 0 !important;
                            border-radius: 0 !important;
                            border-right-width: 0 !important; }
                          table[class=body] .btn table {
                            width: 100% !important; }
                          table[class=body] .btn a {
                            width: 100% !important; }
                          table[class=body] .img-responsive {
                            height: auto !important;
                            max-width: 100% !important;
                            width: auto !important; }}
                        /* -------------------------------------
                            PRESERVE THESE STYLES IN THE HEAD
                        ------------------------------------- */
                        @media all {
                          .ExternalClass {
                            width: 100%; }
                          .ExternalClass,
                          .ExternalClass p,
                          .ExternalClass span,
                          .ExternalClass font,
                          .ExternalClass td,
                          .ExternalClass div {
                            line-height: 100%; }
                          .apple-link a {
                            color: inherit !important;
                            font-family: inherit !important;
                            font-size: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                            text-decoration: none !important; }
                          .btn-primary table td:hover {
                            background-color: #34495e !important; }
                          .btn-primary a:hover {
                            background-color: #34495e !important;
                            border-color: #34495e !important; } }
                </style>
              </head><body class='' style='background-color:#f6f6f6;font-family:sans-serif;-webkit-font-smoothing:antialiased;font-size:14px;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;'>
                <table border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background-color:#f6f6f6;width:100%;'>
                  <tbody>
                    <tr>
                      <td style='font-family:sans-serif;font-size:14px;vertical-align:top;'>&nbsp;</td>
                      <td class='container' style='font-family:sans-serif;font-size:14px;vertical-align:top;display:block;max-width:580px;padding:10px;width:580px;Margin:0 auto !important;'>
                        <div class='content' style='box-sizing:border-box;display:block;Margin:0 auto;max-width:580px;padding:10px;'>
                          <!-- START CENTERED WHITE CONTAINER -->
                          <span class='preheader' style='color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0;'>Your Account has been locked.</span>
                          <table class='main' style='border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;background:#fff;border-radius:3px;width:100%;'>
                            <!-- START MAIN CONTENT AREA -->
                            <tbody>
                              <tr>
                                <td class='wrapper' style='font-family:sans-serif;font-size:14px;vertical-align:top;box-sizing:border-box;padding:20px;'>
                                  <img style='width:100%' src='https://ibin.co/3EUQzKsWuyDY.png' class='center-block img-responsive img-rounded'>
                                  <p style='font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;'></p>
                                  <table border='0' cellpadding='0' cellspacing='0' style='border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;'>
                                    <tbody>
                                      <tr>
                                        <td style='font-family:sans-serif;font-size:14px;vertical-align:top;'>
                                          <p style='font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;'>Hello $FirstName $LastName,</p>
                                          <p style='font-family:sans-serif;font-size:14px;font-weight:normal;margin:0;Margin-bottom:15px;'>Your account has been locked for security reasons. Click the button below to unlock it.</p>
                                          <table border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;box-sizing:border-box;width:100%;'>
                                            <tbody>
                                              <tr>
                                                <td align='center' style='font-family:sans-serif;font-size:14px;vertical-align:top;padding-bottom:15px;'>
                                                  <table border='0' cellpadding='0' cellspacing='0' style='border-collapse:separate;mso-table-lspace:0pt;mso-table-rspace:0pt;width:100%;width:auto;'>
                                                    <tbody>
                                                      <tr>
                                                        <td style='font-family:sans-serif;font-size:14px;vertical-align:top;background-color:#ffffff;border-radius:5px;text-align:center;background-color:#3498db;'>
                                                          <a href='http://localhost/FresnoStateBuyNSell/php/index.php?option=password-reset&user-id=$UserID&hash-token=$HashToken' target='_blank' style='text-decoration:underline;background-color:#ffffff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize;background-color:#3498db;border-color:#3498db;color:#ffffff;'>Unlock Account</a>
                                                        </td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                              <!-- END MAIN CONTENT AREA -->
                            </tbody>
                </table>
                <!-- END CENTERED WHITE CONTAINER -->
                </div>
                </td>
                <td style='font-family:sans-serif;font-size:14px;vertical-align:top;'>&nbsp;</td>
                </tr>
                </tbody>
                </table>


            </body>
            </HTML>
            ";
    }
?>