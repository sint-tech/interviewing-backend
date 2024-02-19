<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<html lang="en">

<head data-id="__react-email-head"></head>

<body data-id="__react-email-body" style="background-color:#f6f9fc;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,Roboto,&quot;Helvetica Neue&quot;,Ubuntu,sans-serif">
<table align="center" width="100%" data-id="__react-email-container" role="presentation" cellSpacing="0" cellPadding="0" border="0" style="max-width:37.5em;background-color:#ffffff;margin:0 auto;padding:20px 0 48px;margin-bottom:64px">
    <tbody>
    <tr style="width:100%">
        <td>
            <table align="center" width="100%" data-id="react-email-section" style="padding:0 48px" border="0" cellPadding="0" cellSpacing="0" role="presentation">
                <tbody>
                <tr>
                    <td><img  alt="sint" src="{{secure_asset('assets/BlueLogo.png')}}" width="85" height="37" style="display:block;outline:none;border:none;text-decoration:none" />
                        <hr style="width:100%;border:none;border-top:1px solid #eaeaea;border-color:#e6ebf1;margin:20px 0" />
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Dear {{sprintf("%s %s", $interview->candidate->first_name, $interview->candidate->last_name)}},</p>
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">We appreciate your interest in joining our company {{$interview->organization->name}} and appreciate your time for taking this interview.</p>
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">This email is to let you know that, while we choose to move on with other candidates, it does not imply that your qualifications are insufficient. We hope you will give us a look for any upcoming roles.</p>
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Check your feedback for future improvement</p><a href="https://usesint.com"  target="_blank" style="background-color:#0036ce;border-radius:5px;color:#fff;font-size:16px;font-weight:bold;text-decoration:none;text-align:center;display:inline-block;width:100%;line-height:100%;max-width:100%;padding:10px 10px"><span><!--[if mso]><i style="letter-spacing: 10px;mso-font-width:-100%;mso-text-raise:15" hidden>&nbsp;</i><![endif]--></span><span style="max-width:100%;display:inline-block;line-height:120%;mso-padding-alt:0px;mso-text-raise:7.5px">Check Feedback</span><span><!--[if mso]><i style="letter-spacing: 10px;mso-font-width:-100%" hidden>&nbsp;</i><![endif]--></span></a>
                        <hr style="width:100%;border:none;border-top:1px solid #eaeaea;border-color:#e6ebf1;margin:20px 0" />
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Thanks,
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">{{$interview->organization->name}}</p>
                        </p>
                        <p style="font-size:16px;line-height:24px;margin:16px 0;color:#525f7f;text-align:left">Powered by Sint </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>
