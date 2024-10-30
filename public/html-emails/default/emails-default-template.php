<!doctype html>  
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Responsive email</title>
    <style type="text/css">
      body {margin: 10px 0; padding: 0 10px; background: #f7f6f6; font-size: 14px !important;}
      table {border-collapse: collapse;}
      td {font-family: arial, sans-serif !important; color: #2E4256 !important;}

      @media only screen and (max-width: 480px) {
        body,table,td,p,a,li,blockquote {
          -webkit-text-size-adjust:none !important;
        }
        table {width: 100% !important;}

        .responsive-image img {
          height: 40px !important;
          max-width: 100% !important;
          width: 100% !important;
        }
      }
    </style>
  </head>
  <body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
      <tr>
        <td>
          <table style="background:#fff; border: 1px solid #e5e5e5; border-radius:4px;" border="0" cellpadding="0" cellspacing="0" align="center" width="640">
            <tr>
              <td border="0" style="font-size: 0; line-height: 0; padding: 0 10px; text-align:left;" height="50" align="left">
                [logo_image]   
              </td>
            </tr>
            <tr>
              <td style="padding: 0px 20px 0px 20px;color: #2E4256;">     
                     
                [email_content]
                
              </td>
            </tr>

            <tr><td style="font-size: 0; line-height: 0;" height="20">&nbsp;</td></tr>
            
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>